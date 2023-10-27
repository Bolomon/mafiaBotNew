<?php
namespace App\Traits;
use App\Models\TelegramUser;

trait SetData 
{
    private $data = [];
    private ?TelegramUser $user = null;
    
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    public function setUser(TelegramUser $user): void
    {
        $this->user = $user;
    }
}