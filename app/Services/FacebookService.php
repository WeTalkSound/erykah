<?php

namespace App\Services;

use App\Contracts\Actor;
use Illuminate\Support\Facades\Http;

class FacebookService
{
    protected const FACEBOOK_MESSENGER_URL = "https://graph.facebook.com/v2.6/me/messages";

    public function sendMessage(Actor $actor)
    {
        $postData = [
            "recipient" => [
                "id" => $actor->getConverser()->identifier
            ],
            "message" => [
                "text" => $actor->talk()
            ]
        ];

        $url = static::FACEBOOK_MESSENGER_URL . "?access_token=" 
                . config("services.facebook.messenger_access_token");

        $response = Http::post($url, $postData);

        \Log::info($response->body());
    }
}