<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LevelScope;
use Log;

class TelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'win',
        'username',
        'first_name',
        'last_name',
        'telegram_id',
    ];

    protected $with = ['schedules'];

    protected $appends = ['scope', 'game_count', 'win_rate', 'ladder_seat'];

    public function getScopeAttribute()
    {
        $scopes = 0;

        $this->userScopes()->get()->each(function ($user) use (&$scopes) {
            $scopes += $user->scope;
        });

        return $scopes;
    }

    public function getLadderSeatAttribute()
    {

        // return \App\Models\UserScope::select(
        //     'telegram_user_id', 
        //     DB::raw('SUM(scope) as total_scope'), 
        //     DB::raw('RANK() OVER (ORDER BY SUM(scope) DESC) as ladder_seat')
        //     )->where('telegram_user_id', $this->id)->groupBy('telegram_user_id')->first()?->ladder_seat ?? 0;
        return DB::table(DB::raw('(
            SELECT 
                telegram_user_id, 
                SUM(scope) as total_scope,
                RANK() OVER (ORDER BY SUM(scope) DESC) as ladder_seat
            FROM telegram_user_scopes
            GROUP BY telegram_user_id
        ) as ranker_scope'))
            ->select('*')
            ->where('telegram_user_id', '=', $this->id)
            ->first()?->ladder_seat ?? 0;
    }

    public function getGameCountAttribute()
    {
        return $this->userScopes()->get()->count();
    }

    public function getWinRateAttribute()
    {
        return $this->userScopes()->where('win', true)->get()->count();
    }

    public function userScopes()
    {
        return $this->hasMany(\App\Models\UserScope::class, 'telegram_user_id');
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_user');
    }
}
