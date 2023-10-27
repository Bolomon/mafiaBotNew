<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Log;
use Telegram\Bot\Api;
use \Telegram\Bot\Objects\Message;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\Game;
use App\Enums\TelegramApiEnum;
use App\Enums\ImagesEnum;
use App\Traits\SetData;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleActionCallback implements CallbackInterface
{
    use SetData;

    private $backButton = [];

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => '🗓 '.$this->data['schedule']['dayOfTheWeek']." ".$this->data['schedule']['date_day']."\n".$this->data['schedule']['date_time']." ".$this->data['schedule']['game']['name'],
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->buttons(),
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
            'caption' => '🗓 '.$this->data['schedule']['dayOfTheWeek']." ".$this->data['schedule']['date_day']."\n".$this->data['schedule']['date_time']." ".$this->data['schedule']['game']['name'],
            'parse_mode' => 'Markdown'
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $this->buttons(),
                'resize_keyboard' => true,
            ]),
        ]);   

        return $response;
    }

    private function buttons():array
    {
        $buttons = [];

        if ($this->user->schedules()->where('schedule_id', $this->data['schedule']['id'])->exists()) {
            $buttons[] = [
                [
                    'text' => 'Отменить запись ❌',
                    'callback_data' => json_encode([
                        'entity' => 'schedule_unrecord',
                        'data' => [
                            'schedule_id' => $this->data['schedule']['id']
                        ]
                    ]),
                ]
            ];
        }else{
            $buttons[] = [
                [
                    'text' => 'Записаться ✅',
                    'callback_data' => json_encode([
                        'entity' => 'schedule_sign_up',
                        'data' => [
                            'schedule_id' => $this->data['schedule']['id']
                        ]
                    ]),
                ]
            ];
        }
        
        $buttons[] = $this->backButton;

        return $buttons;
    }

    public function setData(?array $data): void
    {
        $this->data = [
            'schedule' => Schedule::with(['users', 'game'])
                                    ->select(
                                        '*',
                                        DB::raw('DATE(start_date) as date_day'),
                                        DB::raw("TO_CHAR(start_date, 'HH24:MI') as date_time")
                                    )
                                    ->where('id', $data['schedule_id'])
                                    ->first()
                                    ->toArray()
        ];

        $this->data['schedule']['dayOfTheWeek'] =  $this->dateToDayOfTheWeek($this->data['schedule']['date_day']);

        $this->backButton = [
            [
                'text' => 'Назад',
                'callback_data' => json_encode([
                    'entity' => 'schedule', 
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
        ];;
    }

    private function dateToDayOfTheWeek(string $dateString): string
    {
        $carbonDate = Carbon::parse($dateString);

        $dayOfWeekKey = strtolower($carbonDate->format('l'));
        $dayOfWeekTranslation = Lang::get("days.$dayOfWeekKey");

        return $dayOfWeekTranslation;
    }

}