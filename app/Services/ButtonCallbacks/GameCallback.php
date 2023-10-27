<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use Log;
use App\Models\Game;
use Telegram\Bot\Keyboard\Keyboard;
use \Telegram\Bot\Objects\Message;
use App\Enums\ImagesEnum;
use App\Models\LastMessage;
use App\Traits\SetData;

class GameCallback implements CallbackInterface
{
    use SetData;
    
    public function update(\Telegram\Bot\Api $telegram, int $chatId, int $messageId): Message
    {
        $telegram->editMessageCaption([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $this->data['caption'] ?? ''
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->buttons(2, true),
                'resize_keyboard' => true,
            ]),
        ]);   

        return $response;
    }

    public function send(\Telegram\Bot\Api $telegram, int $chatId): Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => $this->data['caption'] ?? '',
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->buttons(2, true),
                'resize_keyboard' => true,
            ]),
        ]); 

        return $response;   
    }

    private function buttons(int $chunk, bool $withBack): array
    {
        $buttons = [];
        foreach (Game::get() as $game) {
            $buttons[] = [
                'text' => $game->name,
                'callback_data' => $game->callback_data,
            ];
        }

        $buttons = array_chunk($buttons, $chunk);

        if ($withBack) {
            
            $buttons[][] = [
                'text' => 'Главное меню',
                'callback_data' => json_encode([
                    'entity' => 'main_menu', 
                    'data' => null
                ]),
            ];
        }

        return $buttons;
    }
}