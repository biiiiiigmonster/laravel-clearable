<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    use HasFactory;
    use HasClears;
}
