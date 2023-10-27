<?php 

namespace App\Services\Interfaces;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use App\Models\TelegramUser;
use App\Models\LevelScope;

interface RatingInterface 
{
    public function getLevel(int $scope, LevelScope $levelScope): LevelScope;
    public function getPercentages(int $min_scope, int $max_scope, int $scope): int;
    public function getProgressBar(int $percentage): string;
    public function getRatingMessage(int $scope, LevelScope $levelScope): string;
    public function getRank(int $level): ?string;
}