<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'min_scope',
        'max_scope'
    ];
}
