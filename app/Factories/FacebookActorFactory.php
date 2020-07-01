<?php

namespace App\Factories;

use App\Contracts\Actor;
use App\Actors\NullActor;
use App\Models\Converser;
use Illuminate\Support\Facades\Request;

class FacebookActorFactory extends ActorFactory
{
    public static function make(): Actor
    {
        $object = Request::get("object");
        $entries = Request::get("entry");

        if ($object !== "page") {
            return NullActor::make();
        }

        foreach ($entries as $entry) {
            $event = $entry["messaging"][0];
        }

        $senderId = $event["sender"]["id"];
        $message = $event["message"]["text"];

        $self = new static($senderId, $message);

        return $self->actor();
    }
}