<?php
namespace App\Services\ButtonCallbacks;
use App\Models\TelegramUser;
use App\Services\Interfaces\CallbackInterface;
use DB;
use Log;
use App\Traits\SetData;
use \Telegram\Bot\Objects\Message;
use Telegram\Bot\Keyboard\Keyboard;
use App\Enums\ImagesEnum;
use App\Models\LevelScope;
use App\Services\Interfaces\RatingInterface;

class RateCallback implements CallbackInterface
{
    use SetData;

    private $backButton = [];

    public function update(\Telegram\Bot\Api $telegram, int $chatId, int $messageId): Message
    {
        $telegram->editMessageCaption([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $this->dataToMessage($this->data['users']),
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
            'caption' => $this->dataToMessage($this->data['users']),
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

    private function dataToMessage(array $data): string
    {
        $message = "*ТОП 10 ИГРОКОВ :*\n";
        
        foreach ($data as $key => $value) {
            $message .= "*". $key + 1 .".* ". $value['first_name']." ".$value['last_name']."   Cчет: ".$value['scope']."\n";
        }

        return $message;
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
        $this->data['users'] = TelegramUser::select('telegram_users.*', DB::raw('
                                                        (SELECT SUM(scope) 
                                                        FROM telegram_user_scopes 
                                                        WHERE telegram_users.id = telegram_user_scopes.telegram_user_id) 
                                                        AS user_scopes_sum_scope
                                                    '))
                                                    ->where(function($query) {
                                                        $query->whereNotNull(DB::raw('
                                                            (SELECT SUM(scope) 
                                                            FROM telegram_user_scopes 
                                                            WHERE telegram_users.id = telegram_user_scopes.telegram_user_id)
                                                        '));
                                                    })
                                                    ->orderBy(
                                                        DB::raw('
                                                            (SELECT SUM(scope) 
                                                            FROM telegram_user_scopes 
                                                            WHERE telegram_users.id = telegram_user_scopes.telegram_user_id)
                                                        '), 'desc')
                                                    ->limit(10)
                                                    ->get()
                                                    ->toArray();
        
        Log::info('RateCallback $this->data'. json_encode($this->data));

        $this->backButton = [
            'text' => 'Главное меню',
            'callback_data' => json_encode([
                'entity' => 'main_menu', 
                'data' => null
            ]),
        ];
    }
}