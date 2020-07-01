<?php

namespace App\Services;

use App\Contracts\Actor;
use Illuminate\Support\Facades\Http;

class FacebookService
{
    protected const FACEBOOK_MESSENGER_URL = "https://graph.facebook.com/v2.6/me/messages";

    public function sendMessage(Actor $actor)
    {
        $messages = (array) $actor->talk();

        foreach ($messages as $message) {

            $response = Http::post(
                $this->getUrl(), $this->getMessageBody($actor, $message)
            );

            \Log::info($response->body());
        }
    }

    protected function getMessageBody($actor, $message)
    {
        return [
            "recipient" => [
                "id" => $actor->getConverser()->identifier
            ],
            "message" => [
                "text" => $message
            ]
        ];
    }

    protected function getUrl()
    {
        return static::FACEBOOK_MESSENGER_URL . "?access_token=" 
                . config("services.facebook.messenger_access_token");
    }
}