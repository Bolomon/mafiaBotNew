<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Response;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Actions;
use Telegram\Bot\Objects\Update;
use App\Services\ButtonCallbacks\CallBackSwitcher;
use App\Services\ButtonCallbacks\GameCallback;
use App\Enums\ImagesEnum;
use App\Services\ButtonCallbacks\MainMenuCallback;
use App\Models\LastMessage;
use App\Models\TelegramUser;

class WebhookController extends Controller
{
    protected Api $telegram;

    /**
     * Create a new controller instance.
     *
     * @param  Api  $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $update = $this->telegram->commandsHandler(true);

        Log::debug("updates", [$update]);
        
        $chatId = $update->getChat()->id;
        // $lastMessageExists = LastMessage::where('chat_id', $chatId)->exists();

        // Log::debug("lastTest", [$this->telegram->getMessages(['chat_id' => $chatId, 'limit' => 1])]);
        // Log::debug("lastTest", [$update->getChat()]);
        // Log::debug("lastTest", [$update->getChat()]);
        Log::debug("getMessage ", [$update->getMessage()]);

        $user = $update->getMessage();
        
        $telegramUser = TelegramUser::firstOrCreate(
            ['telegram_id' => $user->chat->id],
            [
                'username' => $user->chat->username,
                'first_name' => $user->chat->first_name,
                'last_name' => $user->chat->last_name,
            ]
        );

        if (isset($update->callback_query)) {

            $callbackData = json_decode($update->callback_query->data, true);
            
            $callBack = CallBackSwitcher::getEntity($callbackData['entity']);
            // $message = LastMessage::where('chat_id', $chatId)->first();
            
            Log::debug("callbackData", [$callbackData['data']]);
            Log::debug("telegramUser", [$telegramUser]);

            $callBack->setUser($telegramUser);
            $callBack->setData($callbackData['data']);

            if (in_array('App\\Services\\Interfaces\\ActionInterface', class_implements($callBack))) {
                $callBack->action();
            }

            if ($update->getMessage()->message_id) {
                $response = $callBack->update($this->telegram, $chatId, $update->getMessage()->message_id);
            }else{
                $response = $callBack->send($this->telegram, $chatId);
            }
            
        }else{
            $callBack = new MainMenuCallback();

            $callBack->setUser($telegramUser);
            $response = $callBack->send($this->telegram, $chatId);
        }
        
        $messageId = $response->getMessageId();


        // if ($messageId !== null) {
        //     if ($lastMessageExists) {
        //         $lastMessage = LastMessage::where('chat_id', $chatId)->first();
        //         $lastMessage->update([
        //             'chat_id' => $chatId,
        //             'message_id' => $messageId
        //         ]);
        //     } else {
        //         LastMessage::create([
        //             'chat_id' => $chatId,
        //             'message_id' => $messageId
        //         ]);
        //     }
        // }

        return response([], Response::HTTP_OK);
    }
}
