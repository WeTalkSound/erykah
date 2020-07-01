<?php

namespace App\Actors;

use App\Models\Converser;


class SaluteActor extends Actor
{
    /**
     * should talk
     */
    public static function shouldTalk(Converser $converser, string $message): bool
    {
        return $converser->conversation == null;
    }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        $conversation = "Hey, I can help you get latest information about COVID-19, and also report high risk cases.\n";
        $conversation .= "Send /info - to get latest information about COVID-19. \n";
        $conversation .= "Send  /evaluate - to take a risk assessment test";

        return $conversation;
    }
}