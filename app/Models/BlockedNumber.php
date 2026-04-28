<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedNumber extends Model
{
    protected $fillable = ['phone', 'reason'];
}
