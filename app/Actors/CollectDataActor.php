<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Models\Converser;

class CollectDataActor extends Actor
{

    /**
     * should talk
     */
     public static function shouldTalk(Converser $converser, string $message): bool
     {
        $conversation = $converser->conversation;

        return $conversation && $conversation->collect_data;
     }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        $conversation = $this->converser->conversation;
        $data = $conversation->data;

        $this->fillData($data);

        $conversation->update([
            "data" => $data
        ]);

        foreach ($data as $question => $value) {
            if ($value == null) return $question;
        }

        $message = "Thank you. your information has been fowarded to the relevant agencies";

        return $this->finish($message);
    }

    protected function finish($message)
    {
        $this->converser->conversation->delete();

        return $message;
    }

    protected function fillData(&$data)
    {
        foreach ($data as $question => $value) {
            if ($value == null) {
                return $data[$question] = $this->message;
            }
        }
    }
}