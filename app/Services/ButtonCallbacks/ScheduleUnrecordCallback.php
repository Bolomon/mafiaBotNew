<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use App\Services\Interfaces\ActionInterface;
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
use Illuminate\Support\Facades\Redis;

class ScheduleUnrecordCallback implements CallbackInterface, ActionInterface
{
    use SetData;

    private $backButton = [];

    public function send(Api $telegram, int $chatId): \Telegram\Bot\Objects\Message
    {
        $this->dubleSend($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => "Вы отменили запись на игру 😢\n"
                        ."🗓 *".$this->data['schedule']['dayOfTheWeek']." ".$this->data['schedule']['date_day']." ".$this->data['schedule']['date_time']."\n"
                        .$this->data['schedule']['game']['name']."*\n",
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]);
        
        return $response;
    }

    public function update(Api $telegram, int $chatId, int $messageId): \Telegram\Bot\Objects\Message
    {
        $this->dubleSend($telegram, $chatId);

        $response = $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => "Вы отменили запись на игру 😢\n"
                        ."🗓 *".$this->data['schedule']['dayOfTheWeek']." ".$this->data['schedule']['date_day']." ".$this->data['schedule']['date_time']."\n"
                        .$this->data['schedule']['game']['name']."*\n",
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    $this->backButton
                ],
                'resize_keyboard' => true,
            ]),
        ]);
        
        return $response;
    }

    private function dubleSend(Api $telegram, int $chatId): void
    {
        $telegram->sendPhoto([
            'chat_id' => $chatId,
            'caption' => "Вы отменили запись на игру 😢\n"
                        ."🗓 *".$this->data['schedule']['dayOfTheWeek']." ".$this->data['schedule']['date_day']." ".$this->data['schedule']['date_time']."\n"
                        .$this->data['schedule']['game']['name']."*\n",
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
        ]);
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
                    'entity' => 'schedule_action', 
                    'data' => [
                        'schedule_id' => $this->data['schedule']['id']
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

    private function dateToDayOfTheWeek(string $dateString): string
    {
        $carbonDate = Carbon::parse($dateString);

        $dayOfWeekKey = strtolower($carbonDate->format('l'));
        $dayOfWeekTranslation = Lang::get("days.$dayOfWeekKey");

        return $dayOfWeekTranslation;
    }

    public function action(): void
    {
        $this->user->schedules()->detach($this->data['schedule']['id']);
        $this->setHash();
    }

    private function setHash(): void
    {
        $hash = md5('user:'.$this->user->id.'schedule:'.$this->data['schedule']['id']);
        Redis::hSet("user_hashes", $hash, false);
    }
}