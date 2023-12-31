<?php

namespace App\Jobs;

use App\Models\LastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Telegram\Bot\Api;
use App\Enums\ImagesEnum;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Services\ButtonCallbacks\MainMenuCallback;
use App\Models\TelegramUser;
use App\Models\Schedule;
use Illuminate\Support\Facades\Redis;

class RepeatMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user = null;
    private $schedule = null;
    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $message,
        private int $chatId,
        private int $userId,
        private int $scheduleId
    )
    {
        $this->user = TelegramUser::where("id", $userId)->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->schedule = Schedule::where("id", $this->scheduleId)->first();

        $hash = md5('user:'.$this->userId.'schedule:'.$this->scheduleId);

        // if ($this->user->schedules()->where('schedule_id', $this->scheduleId)->exists()) {
        if ((bool)Redis::hGet('user_hashes', $hash)) {
            if ($this->schedule->min_seats <= $this->schedule->users()->count()) {
                $this->sendMessage("Напоминаем вам, что вы записаны на игру\n".$this->message);
            }elseif ($this->schedule->min_seats > $this->schedule->users()->count()) {
                $this->sendMessage("Игра \n".$this->message."\n отменена, в связи с недостаточным количеством участников");
            }
        }
    }

    private function sendMessage(string $message): void
    {
        // $lastMessage = LastMessage::where('chat_id', $this->chatId)->first();

        // try {
        //     Telegram::bot()->deleteMessage([
        //         'chat_id' => $this->chatId,
        //         'message_id' => $lastMessage->message_id,
        //     ]);
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

        Telegram::sendPhoto([
            'chat_id' => $this->chatId,
            'caption' => $message,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(ImagesEnum::MAFIA->getImage()),
            'parse_mode' => 'Markdown',
        ]);

        $callBack = new MainMenuCallback();

        $response = $callBack->send(Telegram::bot(), $this->chatId);

        // $lastMessage->update([
        //     'chat_id' => $this->chatId,
        //     'message_id' => $response->message_id
        // ]);
    }
}
