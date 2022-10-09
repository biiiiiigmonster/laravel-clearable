<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use BiiiiiigMonster\Clearable\Tests\Clears\PostVotesOddClear;
use BiiiiiigMonster\Clearable\Tests\Clears\RoleExceptSystemTypeClear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class UserPropertyCustom extends Model
{
    use HasFactory;
    use HasClears;

    protected $table = 'users';

    protected $clears = [
        'posts' => PostVotesOddClear::class,
        'roles' => RoleExceptSystemTypeClear::class,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }

    public function history(): HasOne
    {
        return $this->hasOne(History::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, RoleUser::class, 'user_id')->withTimestamps()->withPivot('type');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
