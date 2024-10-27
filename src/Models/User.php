<?php

namespace Iransh\Wallet\Models;

use App\Models\User as MainUser;
//use Iransh\Wallet\Database\Factories\UserFactory;
use Database\Factories\UserFactory;

class User extends MainUser
{

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
