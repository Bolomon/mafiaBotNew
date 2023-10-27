<?php
namespace App\Services\ButtonCallbacks;
use App\Services\Interfaces\CallbackInterface;
use App\Services\ButtonCallbacks;

class CallBackSwitcher {
    public static function getEntity(string $callBackName): CallbackInterface|bool
    {
        if ($callBackName == 'games') {
            return new GameCallback();
        }else if ($callBackName == 'rules') {
            return new RulesCallback();
        }else if ($callBackName == 'payment') {
            return new PaymentCallback();
        }else if ($callBackName == 'rate') {
            return new RateCallback();
        }else if ($callBackName == 'faq') {
            return new FaqCallback();
        }else if ($callBackName == 'schedule') {
            return new ScheduleCallback();
        }else if ($callBackName == 'profile') {
            return new ProfileCallback();
        }else if ($callBackName == 'main_menu') {
            return new MainMenuCallback();
        }else if ($callBackName == 'gameInfo') {
            return new GameInfoCallback();
        }else if ($callBackName == 'schedule_action') {
            return new ScheduleActionCallback();
        }else if ($callBackName == 'schedule_sign_up') {
            return new ScheduleSignUpCallback();
        }else if ($callBackName == 'schedule_unrecord') {
            return new ScheduleUnrecordCallback();
        }
               
        return false;
    }
}