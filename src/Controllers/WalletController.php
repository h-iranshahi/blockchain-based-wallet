<?php

namespace Iransh\Wallet\Controllers;

use App\Http\Controllers\Controller;
use Iransh\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Iransh\Wallet\Events\TransactionCreated;
use Iransh\Wallet\Models\User;
use Iransh\Wallet\Models\Blockchain;

class WalletController extends Controller
{
    // Get the current balance and transactions
    public function balance()
    {
        $user = Auth::user();

        return response()->json([
            'balance' => $user->wallet->balance,
            'transactions' => $user->wallet->transactions // Fetch transaction history
        ]);
    }

    public function deposit(Request $request)
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        DB::transaction(function () use ($request, $wallet) {
            // Increase wallet balance
            $wallet->balance += $request->amount;
            $wallet->save();

            // Create a transaction
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'deposit'
            ]);

            // Record the transaction in the blockchain
            Blockchain::createBlock([
                'type' => 'deposit',
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'timestamp' => now(),
            ]);

            // Broadcast the event
            event(new TransactionCreated($transaction));
        });

        return response()->json(['message' => 'Deposit successful', 'balance' => $wallet->balance]);
    }

    public function withdraw(Request $request)
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        DB::transaction(function () use ($request, $wallet) {
            // Deduct from wallet balance
            $wallet->balance -= $request->amount;
            $wallet->save();

            // Create a transaction
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'withdraw'
            ]);

            // Record the transaction in the blockchain
            Blockchain::createBlock([
                'type' => 'withdraw',
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'timestamp' => now(),
            ]);

            // Broadcast the event
            event(new TransactionCreated($transaction));
        });

        return response()->json(['message' => 'Withdraw successful', 'balance' => $wallet->balance]);
    }

    // Transfer money to another user
    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:'.(new User)->getTable().',id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $sender = User::find(Auth::user()->id);
        $senderWallet = $sender->wallet;
        $amount = $request->amount;

        if ($senderWallet->balance < $amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        // Fetch the recipient and their wallet
        $recipient = User::findOrFail($request->recipient_id);
        $recipientWallet = $recipient->wallet;

        DB::transaction(function () use ($senderWallet, $recipientWallet, $amount) {
            // Deduct from sender's wallet
            $senderWallet->balance -= $amount;
            $senderWallet->save();

            // Add to recipient's wallet
            $recipientWallet->balance += $amount;
            $recipientWallet->save();

            // Create a debit transaction for sender
            $senderTransaction = Transaction::create([
                'wallet_id' => $senderWallet->id,
                'amount' => -$amount,
                'type' => 'transfer',
                'description' => 'Transfer to wallet ID ' . $recipientWallet->id
            ]);

            // Create a credit transaction for recipient
            $recipientTransaction = Transaction::create([
                'wallet_id' => $recipientWallet->id,
                'amount' => $amount,
                'type' => 'transfer',
                'description' => 'Received from wallet ID ' . $senderWallet->id
            ]);

            // Fire the TransactionCreated event for both transactions
            event(new TransactionCreated($senderTransaction));
            event(new TransactionCreated($recipientTransaction));

            // Record the transfer on the blockchain
            Blockchain::createBlock([
                'type' => 'transfer',
                'from_wallet_id' => $senderWallet->id,
                'to_wallet_id' => $recipientWallet->id,
                'amount' => $amount,
                'timestamp' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Transfer successful',
            'sender_balance' => $senderWallet->fresh()->balance,
            'recipient_balance' => $recipientWallet->fresh()->balance,
        ]);
    }
}
