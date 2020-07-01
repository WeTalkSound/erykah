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

        return [ 
                    "Thats awesome! I've saved your email, so tell me, what do you want to know?" => [ 
                        [
                            "content_type" => "text",
                            "title" => "My Next Event",
                            "payload" => NextEventActor::class
                        ], [
                            "content_type" => "text",
                            "title" => "My Latest Music",
                            "payload" => LatestMusicActor::class
                        ], [

                            "content_type" => "text",
                            "title" => "My Bio",
                            "payload" => BioActor::class
                            ]
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