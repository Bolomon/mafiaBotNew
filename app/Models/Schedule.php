<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'seats',
        'address',
        'min_seats',
        'address_link',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function users()
    {
        return $this->belongsToMany(TelegramUser::class, 'schedule_user', 'schedule_id', 'telegram_user_id', 'id', 'id');
    }
}
