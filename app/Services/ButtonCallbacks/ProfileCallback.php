<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use Log;
use App\Traits\SetData;
use \Telegram\Bot\Objects\Message;
use Telegram\Bot\Keyboard\Keyboard;
use App\Enums\ImagesEnum;
use App\Models\LevelScope;
use App\Services\Interfaces\RatingInterface;

class ProfileCallback implements CallbackInterface
{
    use SetData;

    private $backButton = [];

    private function ratingMessage()
    {
        $rating = app('App\Services\Interfaces\RatingInterface');
        $ratingString = $rating->getRatingMessage($this->user->scope, new LevelScope);
        $ratingString = "*".$this->user->username."*\n$ratingString\n";
        $ratingString = $ratingString."Место в общем рейтинге: *".$this->user->ladder_seat."*\nСделано ходок: *".$this->user->game_count."*\n"."Количество побед: *".$this->user->win_rate."*";
        return $ratingString;
    }

    public function update(\Telegram\Bot\Api $telegram, int $chatId, int $messageId): Message
    {
        $telegram->editMessageCaption([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $this->ratingMessage(),
            'parse_mode' => 'Markdown',
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        $this->backButton
                    ]
                ],
                'resize_keyboard' => true,
            ]),
        ]);   

        return $response;
    }

    public function send(\Telegram\Bot\Api $telegram, int $chatId): Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => $this->ratingMessage(),
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::PROFILE_DEFAULT->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        $this->backButton
                    ]
                ],
                'resize_keyboard' => true,
            ]),
            'parse_mode' => 'Markdown',
        ]); 

        return $response;   
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
        $this->backButton = [
            'text' => 'Главное меню',
            'callback_data' => json_encode([
                'entity' => 'main_menu', 
                'data' => null
            ]),
        ];
    }
}