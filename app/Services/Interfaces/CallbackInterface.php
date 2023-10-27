<?php 

namespace App\Services\Interfaces;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use App\Models\TelegramUser;

interface CallbackInterface {

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message;

    public function update(Api $telegram, int $chatId, int $messageId): \Telegram\Bot\Objects\Message;

    public function setData(?array $data): void;

    public function setUser(TelegramUser $user): void;
}