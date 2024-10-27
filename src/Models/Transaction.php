<?php

namespace Iransh\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'type', 'amount', 'related_wallet_id', 'description'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
