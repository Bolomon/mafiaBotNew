<?php
namespace App\Services\ButtonCallbacks;
use App\Models\CallBackData;
use App\Services\Interfaces\CallbackInterface;
use Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Keyboard\Keyboard;
use App\Traits\SetData;
use App\Enums\TelegramApiEnum;
use App\Enums\ImagesEnum;

class FaqCallback implements CallbackInterface
{
    use SetData;

    private $backButton = [];

    public function __construct()
    {
        $this->backButton = [
            'text' => 'Главное меню',
            'callback_data' => json_encode([
                'entity' => 'main_menu', 
                'data' => null
            ]),
        ];
    }
    
    public function update(\Telegram\Bot\Api $telegram, int $chatId, int $messageId): Message
    {
        // try {
        //     $response = $telegram->deleteMessage([
        //         'chat_id' => $chatId,
        //         'message_id' => $messageId,
        //     ]);
        // } catch (\Throwable $th) {

        // }
        

        $this->sendFaqMessages($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
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
        $this->sendFaqMessages($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
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

    private function sendFaqMessages(\Telegram\Bot\Api $telegram, int $chatId)
    {
        $rule = $this->getFaqMessages();

        foreach ($rule as $ruleItem) {
            $response = $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $ruleItem,
                'parse_mode' => 'Markdown',
            ]);
        }
    }
    
    private function getFaqMessages()
    {
        return str_split($this->data->text, TelegramApiEnum::MESSAGE->maxLength()) ?? [''];
    }

    public function setData(?array $data): void
    {
        $this->data = json_decode(CallBackData::whereEntity(self::class)->first()->data);
    }
}