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
    public function talk()
    {
        $conversation[] = "Hey there! I'm Erykah's chatbot.";
        $conversation[] = "I can tell you about my latest music, when my next event is happening and a bit of info about myself";
        $conversation[] = "but, firstly, let me have your email so we can keep in touch.";

        return $conversation;
    }
}