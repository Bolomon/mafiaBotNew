<?php
namespace App\Enums;

enum ImagesEnum 
{
    case MAFIA;
    case PROFILE_DEFAULT;
    
    public function getImage(): string {
        return match ($this) {
            self::MAFIA => env('APP_URL').'/storage/mafia.jpg',
            self::PROFILE_DEFAULT => env('APP_URL').'/storage/lika.jpg',
        };
    }
}