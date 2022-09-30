<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Supplier extends Model
{
    use HasFactory;
    use HasClears;

    public function userHistory(): HasOneThrough
    {
        return $this->hasOneThrough(History::class, User::class);
    }
}
