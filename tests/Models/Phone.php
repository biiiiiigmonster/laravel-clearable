<?php

namespace BiiiiiigMonster\Clearable\Tests\Models;

use BiiiiiigMonster\Clearable\Concerns\HasClears;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;
    use HasClears;
}
