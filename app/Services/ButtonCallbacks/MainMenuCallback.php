<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use Log;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use App\Enums\ImagesEnum;
use App\Models\LastMessage;
use App\Traits\SetData;

class MainMenuCallback implements CallbackInterface
{
    use SetData;
    private $inlineKeyboard = [];
    public function __construct()
    {
        $this->inlineKeyboard = [
            [
                [
                    'text' => 'Игры',
                    'callback_data' => json_encode([
                        'entity' => 'games', 
                        'data' => null
                    ]),
                ],
                [
                    'text' => 'Расписание',
                    'callback_data' => json_encode([
                        'entity' => 'schedule', 
                        'data' => null
                    ]),
                ],
            ],
            [
                [
                    'text' => 'Цены',
                    'callback_data' => json_encode([
                        'entity' => 'payment', 
                        'data' => null
                    ]),
                ],
                [
                    'text' => 'Рейтинг',
                    'callback_data' => json_encode([
                        'entity' => 'rate', 
                        'data' => null
                    ]),
                ],
            ],
            [
                [
                    'text' => 'FAQ',
                    'callback_data' => json_encode([
                        'entity' => 'faq', 
                        'data' => null
                    ]),
                ],
                [
                    'text' => 'Правила',
                    'callback_data' => json_encode([
                        'entity' => 'rules', 
                        'data' => null
                    ]),
                ],
            ],
            [
                [
                    'text' => 'Профиль игрока',
                    'callback_data' => json_encode([
                        'entity' => 'profile', 
                        'data' => null
                    ]),
                ],
            ]
        ];
    }

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => '',
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->inlineKeyboard,
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
            'caption' => ''
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->inlineKeyboard,
                'resize_keyboard' => true,
            ]),
        ]);   

        return $response;
    }
}