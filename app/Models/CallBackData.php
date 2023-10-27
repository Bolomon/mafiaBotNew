<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallBackData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'entity'
    ];
}
