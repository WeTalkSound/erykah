<?php

namespace App\Actors;

use App\Models\Converser;
use App\Models\Conversation;


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
        $this->createConversation();

        $conversation[] = "Hey there! I'm Erykah's chatbot.";
        $conversation[] = "I can tell you about my latest music, when my next event is happening and a bit of info about myself";
        $conversation[] = "But, firstly, let me have your email so we can keep in touch.";

        return $conversation;
    }

    protected function createConversation()
    {
        $this->getConverser()->conversation()->save(
            new Conversation([
                'actor' => EmailActor::class, 
                'refering_actor' => static::class
            ])
        );
    }
}