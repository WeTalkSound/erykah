<?php

namespace App\Factories;

use App\Contracts\Actor;
use App\Models\Converser;
use Illuminate\Support\Facades\Request;

class WhatsappActorFactory extends ActorFactory
{
    public static function make(): Actor
    {
        $self = new static(
            Request::get("From"), 
            Request::get("Body"));

        return $self->actor();
    }
}