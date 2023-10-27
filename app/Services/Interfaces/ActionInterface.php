<?php 

namespace App\Services\Interfaces;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use App\Models\TelegramUser;

interface ActionInterface {
    public function action(): void;
}