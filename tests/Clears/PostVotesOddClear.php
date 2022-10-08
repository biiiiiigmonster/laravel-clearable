<?php


namespace BiiiiiigMonster\Clearable\Tests\Clears;


use BiiiiiigMonster\Clearable\Contracts\ClearsAttributes;
use Illuminate\Database\Eloquent\Model;

class PostVotesOddClear implements ClearsAttributes
{
    public function abandon(Model $clear): bool
    {
        return $clear->votes % 2;
    }
}