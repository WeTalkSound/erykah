<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookService;
use App\Actors\NullActor;
use App\Factories\FacebookActorFactory;

class FacebookController extends Controller
{
    const VERIFY_TOKEN = "facebook-erykah-bot-verified";

    public function verify(Request $request)
    {
        $mode = $request->input("hub_mode");
        $token = $request->input("hub_verify_token");
        $challenge = $request->input("hub_challenge");

        return $mode && $token && $mode === "subscribe" &&
                $token == static::VERIFY_TOKEN ?
                response($challenge, 200) : response("", 403);
    }

    public function __invoke(FacebookService $service)
    {
        $actor = FacebookActorFactory::make();

        if ($actor instanceOf NullActor) {
            return response($actor->talk(), 422);
        }

        $service->sendMessage($actor);

        return response("sent", 200);
    }
}
