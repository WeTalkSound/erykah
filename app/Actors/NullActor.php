<?php

namespace App\Actors;

use App\Models\Converser;


class NullActor extends Actor
{
    /**
     * should talk
     */
    public static function shouldTalk(Converser $converser, string $message): bool
    {
        return false;
    }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        return "Cannot process conversation";
    }

    public static function make()
    {
        return new static(Converser::make(), "");
    }
}