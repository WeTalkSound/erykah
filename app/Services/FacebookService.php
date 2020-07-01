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

        foreach ($messages as $title => $message) {

            $response = Http::post(
                $this->getUrl(), $this->getMessageBody($actor, $title, $message)
            );

            \Log::info($response->body());
        }
    }

    protected function getMessageBody($actor, $title, $message)
    {
        $messageBody['recipient'] = [
            "id" => $actor->getConverser()->identifier
        ];

        $hasQuickReplies = is_array($message);

        $text  = $hasQuickReplies ? $title : $message;

        if ($hasQuickReplies) {

            $messageBody['messaging_type'] = 'RESPONSE';

            $messageBody['message'] = [
                "text" => $text,
                "quick_replies" => $message
            ];

            return $messageBody;
        }

        $messageBody['message'] = [
            "text" => $text
        ];

        return $messageBody;
    }

    protected function getUrl()
    {
        return static::FACEBOOK_MESSENGER_URL . "?access_token=" 
                . config("services.facebook.messenger_access_token");
    }
}