<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScope extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
    ];

    protected $table = 'telegram_user_scopes';

    public function game()
    {
        $this->hasOne(\App\Models\Game::class);
    }
}
