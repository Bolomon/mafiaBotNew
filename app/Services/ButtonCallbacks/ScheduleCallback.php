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

class ScheduleCallback implements CallbackInterface
{
    use SetData;
    
    private $backButton = [];

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message
    {
        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => $this->dataToMessage($this->dataFormated($this->data)),
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    ...$this->keyboardData($this->data),
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
            'caption' => $this->dataToMessage($this->dataFormated($this->data)),
            'parse_mode' => 'Markdown'
        ]);

        $response = $telegram->editMessageReplyMarkup([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    ...$this->keyboardData($this->data),
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

        $schedule = Schedule::with(['users', 'game'])
                                ->select(
                                    '*',
                                    DB::raw('DATE(start_date) as date_day'),
                                    DB::raw("TO_CHAR(start_date, 'HH24:MI') as date_time")
                                )
                                ->where('start_date', '>', now()->addHour())
                                ->orderBy('start_date', 'asc')
                                ->get()->toArray();

        $this->data = $schedule;
        
        $this->backButton = [
            'text' => 'Главное меню',
            'callback_data' => json_encode([
                'entity' => 'main_menu', 
                'data' => null
            ]),
        ];
    }

    private function dataFormated($data)
    {
        $result = [];
        foreach ($data as $value) {
            $result[$value['date_day']][] = $value;
        }

        return $result;
    }

    private function dataToMessage(array $formatedData): string
    {
        $message = "";
        
        foreach ($formatedData as $key => $value) {
            $message .= "\n🗓 " . $this->dateToDayOfTheWeek($key)." (".$key .") \n";
            foreach ($value as $scheduleItem) {
                $message .= $scheduleItem['date_time']." ".$scheduleItem['game']['name']." *(".count($scheduleItem['users'])."/".$scheduleItem['seats'].")*\n";
            }
        }

        return $message;
    }

    private function keyboardData(array $data): array
    {
        $keyboardData = [];
        
        foreach ($data as $value) {
            $userIds = array_column($value['users'], 'id');

            if (count($value['users']) < $value['seats'] || in_array($this->user->id, $userIds)) {
                $keyboardData[] = [[
                    'text' => $value['date_day']." ".$value['date_time']." ".$value['game']['name'],
                    'callback_data' => json_encode([
                        'entity' => 'schedule_action', 
                        'data' => [
                            'schedule_id' => $value['id'] 
                        ]
                    ]),
                ]];
            }
        }
        
        return $keyboardData;
    }

    private function dateToDayOfTheWeek(string $dateString): string
    {
        $carbonDate = Carbon::parse($dateString);

        $dayOfWeekKey = strtolower($carbonDate->format('l'));
        $dayOfWeekTranslation = Lang::get("days.$dayOfWeekKey");

        return $dayOfWeekTranslation;
    }
}