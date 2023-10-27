<?php
namespace App\Services\ButtonCallbacks;
use App\Models\CallBackData;
use App\Services\Interfaces\CallbackInterface;
use Log;
use App\Traits\SetData;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use App\Enums\ImagesEnum;

class PaymentCallback implements CallbackInterface
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

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => $this->data->text,
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

    public function update(Api $telegram, int $chatId, int $messageId): \Telegram\Bot\Objects\Message
    {
        $telegram->editMessageCaption([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $this->data->text
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

    public function setData(?array $data): void
    {
        $this->data = json_decode(CallBackData::whereEntity(self::class)->first()->data);
    }
}