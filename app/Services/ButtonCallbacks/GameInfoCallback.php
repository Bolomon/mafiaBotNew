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

class GameInfoCallback implements CallbackInterface
{
    use SetData;
    private $backButton = [];

    public function __construct()
    {
        $this->backButton = [
            [
                'text' => 'Назад',
                'callback_data' => json_encode([
                    'entity' => 'games', 
                    'data' => null
                ]),
            ],
            [
                'text' => 'Главное меню',
                'callback_data' => json_encode([
                    'entity' => 'main_menu', 
                    'data' => null
                ])
            ]
        ];
    }
    
    public function update(\Telegram\Bot\Api $telegram, int $chatId, int $messageId): Message
    {

        $telegram->editMessageCaption([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => isset($this->data['slug']) ? $this->getDescBySlug($this->data['slug']) : ''
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        $this->ruleButton()
                    ],
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]);

        return $response;
    }

    public function send(\Telegram\Bot\Api $telegram, int $chatId): Message
    {
        $response = $telegram->sendPhoto([
            'caption' => $this->getDescBySlug($this->data['slug']),
            'chat_id' => $chatId,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        $this->ruleButton()
                    ],
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]); 

        return $response;
    }

    private function getDescBySlug($slug): string
    {
        return Game::where('slug', $slug)->first()->description;
    }

    private function getNameBySlug($slug): string
    {
        return Game::where('slug', $slug)->first()->name;
    }

    private function ruleButton(): array
    {
        return [
            'text' => 'Правила',
            'callback_data' => json_encode([
                'entity' => 'rules', 
                'data' => [
                    'slug' => $this->data['slug'],
                ]
            ]),
        ];
    }
}