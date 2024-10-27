<?php

namespace Iransh\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Iransh\Wallet\Database\Factories\WalletFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {   
        return $this->hasMany(Transaction::class);
    }

    protected static function newFactory()
    {
        return WalletFactory::new();
    }
}
