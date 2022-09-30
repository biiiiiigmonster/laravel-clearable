<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory;
    use HasClears;

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class);
    }
}
