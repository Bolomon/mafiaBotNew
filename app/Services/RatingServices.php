<?php 

namespace App\Services;

use Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use App\Models\TelegramUser;
use App\Models\LevelScope;
use App\Services\Interfaces\RatingInterface;

class RatingServices implements RatingInterface
{
    const USER_RANK = [
        'Новичок' => [
            'min' => 1,
            'max' => 10
        ],
        'Продолжабщий' => [
            'min' => 11,
            'max' => 20
        ],
        'Профи' => [
            'min' => 21,
            'max' => 30
        ],
        'Легенда' => [
            'min' => 31,
            'max' => 999999
        ],
    ];

    /**
     * @param int $scope
     * @param \App\Models\LevelScope $levelScope
     * @return string
     */
    public function getRatingMessage(int $scope, LevelScope $levelScope): string
    {
        $levelData = $this->getLevel($scope, $levelScope);
        $percentages = $this->getPercentages($levelData->min_scope, $levelData->max_scope, $scope);
        $progressBar = $this->getProgressBar($percentages);
        $rank = $this->getRank($levelData->level);
        
        $max_scope = $levelData->max_scope - $levelData->min_scope + 1;
        $totalScope = $scope;
        $scope = $totalScope - $levelData->min_scope;
        
        return "*$percentages%* $progressBar *$scope / $max_scope*\n\nЗвездный Рейтинг: *$levelData->level*\nИнтеллектуальная Вершина: *$rank*\nВсего очков: *$totalScope*";
    }

    /**
     * @param int $level
     * @return mixed
     */
    public function getRank(int $level): ?string
    {
        foreach (self::USER_RANK as $rank => $range) {
            if ($level >= $range['min'] && $level <= $range['max']) {
                return $rank;
            }
        }
        return null;
    }

    /**
     * @param int $scope
     * @param \App\Models\LevelScope $levelScope
     * @return \App\Models\LevelScope
     */
    public function getLevel(int $scope, LevelScope $levelScope): LevelScope
    {
        $level = $levelScope->where([
            ['max_scope', '>=' , $scope],
            ['min_scope', '<=' , $scope]
        ])->first();
        
        return $level;
    }

    /**
     * @param int $min_scope
     * @param int $max_scope
     * @param int $scope
     * @return int
     */
    public function getPercentages(int $min_scope, int $max_scope, int $scope): int
    {
        if ($max_scope == $min_scope) {
            return 100;
        }

        $scope = max($min_scope, min($max_scope, $scope));
        $percentage = (($scope - $min_scope) / ($max_scope - $min_scope)) * 100;

        return round($percentage);
    }

    /**
     * @param int $percentage
     * @return string
     */
    public function getProgressBar(int $percentage): string
    {
        $percentage = max(0, min(100, $percentage));

        $blocks = round($percentage / 10);
        $emptyBlocks = 10 - $blocks;
        $progressBar = '|' . str_repeat('▉', $blocks) . str_repeat('--', $emptyBlocks) . '|';
    
        return $progressBar;
    }
}