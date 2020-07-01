<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Models\Converser;

class BadResponseActor extends Actor
{

    /**
     * should talk
     */
     public static function shouldTalk(Converser $converser, string $message): bool
     {
        $conversation = $converser->conversation;

        $conversation && $expected_answers = array_keys($conversation->expected_answers);

        return $conversation && $conversation->question && !in_array($message, $expected_answers);
     }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        $convo = "You should respond with: \n";
        foreach ($this->converser->conversation->expected_answers as $option => $value) {
            $convo .= $option . "\n";
        }

        return $convo;
    }
}