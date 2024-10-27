<?php

namespace Iransh\Wallet\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Iransh\Wallet\Models\Wallet;
use Iransh\Wallet\Models\Transaction;
use Iransh\Wallet\Models\Blockchain;
use Iransh\Wallet\Models\User;
use Iransh\Wallet\Events\TransactionCreated;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable notifications and events for all tests
        Notification::fake();
        Event::fake([TransactionCreated::class]);
    }

    public function test_transfer_requires_recipient_id_and_amount()
    {
        $sender = User::factory()->create();
        $this->actingAs($sender);

        $response = $this->postJson('/api/wallet/transfer', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['recipient_id', 'amount']);
    }

    public function test_transfer_fails_with_insufficient_balance()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        
        Wallet::factory()->create(['user_id' => $sender->id, 'balance' => 50]);  
        Wallet::factory()->create(['user_id' => $recipient->id, 'balance' => 0]);  

        $this->actingAs($sender);

        $response = $this->postJson('/api/wallet/transfer', [
            'recipient_id' => $recipient->id,
            'amount' => 100
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Insufficient funds']);
    }

    public function test_successful_transfer()
    {
        $sender = User::factory()->create();
        $senderWallet = Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 200,
        ]);

        $recipient = User::factory()->create();
        $recipientWallet = Wallet::factory()->create([
            'user_id' => $recipient->id,
            'balance' => 100,
        ]);

        $this->actingAs($sender);

        $response = $this->postJson('/api/wallet/transfer', [
            'recipient_id' => $recipient->id,
            'amount' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Transfer successful',
        ]);

        // Assert sender's and recipient's wallet balances are updated
        $this->assertEquals(150, $senderWallet->fresh()->balance);
        $this->assertEquals(150, $recipientWallet->fresh()->balance);

        // Assert transactions are created
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $senderWallet->id,
            'amount' => -50,
            'type' => 'transfer',
        ]);
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $recipientWallet->id,
            'amount' => 50,
            'type' => 'transfer',
        ]);

        // Assert blockchain record is created
        $this->assertDatabaseHas('blockchains', [
            'index' => 0,
            'previous_hash' => '0',
        ]);

        // Assert that TransactionCreated event was fired twice
        Event::assertDispatched(TransactionCreated::class, 2);
    }

    public function test_transfer_throws_error_for_invalid_recipient_id()
    {
        $sender = User::factory()->create();
        $senderWallet = Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 100,
        ]);

        $this->actingAs($sender);

        $response = $this->postJson('/api/wallet/transfer', [
            'recipient_id' => 999, // Non-existent user ID
            'amount' => 50,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['recipient_id']);
    }

    public function test_transfer_throws_error_for_invalid_amount()
    {
        $sender = User::factory()->create();
        $senderWallet = Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 200,
        ]);
        $recipient = User::factory()->create();
        
        $this->actingAs($sender);

        // Test with invalid negative amount
        $response = $this->postJson('/api/wallet/transfer', [
            'recipient_id' => $recipient->id,
            'amount' => -10,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }
}
