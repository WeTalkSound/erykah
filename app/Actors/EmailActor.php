<?php

namespace App\Actors;

use App\Models\Converser;
use Illuminate\Support\Facades\Validator;


class EmailActor extends Actor
{
    /**
     * should talk
     */
    public static function shouldTalk(Converser $converser, string $message): bool
    {
        $conversation = $converser->conversation;

        return $conversation && $conversation->actor == static::class;
    }

     /**
     * Converse
     * @return $message
     */
    public function talk()
    {
        if (! $this->isValidEmail()) {
            return "Hmm... This doesn't look like a valid email";
        }
        $this->createConversation();

        return [ "Awesome! I've saved your email. So tell me, what do you want to know?",
            "postback" => [
                "Next Event",
                "Latest Music",
                "About me"
            ]
            ];
    }

    protected function isValidEmail()
    {
        $validator = Validator::make(
            ['email' => $this->message], 
            ['email' => 'email']
        );

        return $validator->passes();
    }

    protected function createConversation()
    {
        $this->converser->conversation->delete();
    }
}