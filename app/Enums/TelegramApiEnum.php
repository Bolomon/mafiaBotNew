<?php
namespace App\Enums;

enum TelegramApiEnum
{
    case MESSAGE;
    case CAPTION;
    
    public function maxLength(): string {
        return match ($this) {
            self::MESSAGE => 4096,
            self::CAPTION => 1024,
        };
    }
}