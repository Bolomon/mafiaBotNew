<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use Log;
use Telegram\Bot\Api;
use \Telegram\Bot\Objects\Message;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\Game;
use App\Enums\TelegramApiEnum;
use App\Enums\ImagesEnum;
use App\Traits\SetData;

class RulesCallback implements CallbackInterface
{
    use SetData;
    
    private $backButton = [];
    public function send(Api $telegram, int $chatId): Message
    {
        $this->sendruleMessages($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]); 

        return $response;   
    }

    public function update(Api $telegram, int $chatId, int $messageId): Message
    {
        $response = $telegram->deleteMessage([
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);

        $this->sendruleMessages($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]); 

        return $response;
    }

    private function sendruleMessages(Api $telegram, int $chatId)
    {
        $rule = $this->getrule($this->data['slug']);
        $ruleDocuments = $this->getruleDocuments($this->data['slug']);

        foreach ($rule as $ruleItem) {
            $response = $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $ruleItem
            ]);
        }

        foreach ($ruleDocuments as $ruleDocument) {
            $response = $telegram->sendDocument([
                'chat_id' => $chatId,
                'document' => \Telegram\Bot\FileUpload\InputFile::create($ruleDocument['original_url']),
            ]);
        }
    }

    public function setData(?array $data): void
    {
        $this->data = $data;

        $this->backButton = 
        [
            [
                'text' => 'Назад',
                'callback_data' => json_encode([
                    'entity' => 'gameInfo', 
                    'data' => [
                        'slug' => $data['slug']
                    ]
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

    private function getrule($slug)
    {
        $text = Game::where('slug', $slug)->with('rule')->first()->rule->text;

        return str_split($text, TelegramApiEnum::MESSAGE->maxLength()) ?? [''];
    }

    private function getruleDocuments($slug)
    {
        $documents = Game::where('slug', $slug)->with('rule')->first()->rule->getMedia()->toArray();
        
        return $documents;
    }
}