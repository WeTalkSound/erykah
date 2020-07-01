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

        foreach ($messages as $type => $message) {

            $response = Http::post(
                $this->getUrl(), $this->getMessageBody($actor, $type, $message)
            );

            \Log::info($response->body());
        }
    }

    protected function getMessageBody($actor, $type, $message)
    {
        $messageBody['recipient'] = [
            "id" => $actor->getConverser()->identifier
        ];

        $messageBody['message'] = [
            "text" => $message
        ];

        if ($type == 'postback') {
            $messageBody['message'] = [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'button',
                        'text' => 'testing buttons',
                        'buttons' => $this->getPostbackButtons($message)
                    ]
                ]
                    ];
        }

        return $messageBody;
    }

    protected function getPostbackButtons($message)
    {
        $messages = (array) $message;

        foreach ($messages as $msg) {
            $postBackButtons[] = [
                'type' => 'postback',
                'title' => $msg,
                'payload' => $msg
            ];
        }

        return $postBackButtons ?? [];
    }

    protected function getUrl()
    {
        return static::FACEBOOK_MESSENGER_URL . "?access_token=" 
                . config("services.facebook.messenger_access_token");
    }
}