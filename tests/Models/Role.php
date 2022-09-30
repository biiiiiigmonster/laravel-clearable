<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;
    use HasClears;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(RoleUser::class)->withTimestamps();
    }
}
