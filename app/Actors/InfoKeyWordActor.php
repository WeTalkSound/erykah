<?php

namespace App\Actors;

use App\Models\Converser;
use App\Models\CacheInfo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class InfoKeyWordActor extends Actor
{
    /**
     * should talk
     */
    public static function shouldTalk(Converser $converser, string $message): bool
    {
        return $message == "/info";
    }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        return $this->getFromCache() ?? $this->getFresh();
        
    }

    protected function getFromCache()
    {
        $cacheInfo = CacheInfo::whereDate(
            "info_date", now()->format("Y-m-d")
        )->first();

        return $cacheInfo ? $cacheInfo->info : null;
    }

    protected function getFresh()
    {
        [$confirmedCases, $date] = $this->fetchConfirmedInfo();
        $deathCases = $this->fetchDeathInfo();
        $death = Str::plural("death", $deathCases);

        $conversation = "As at $date, there have been $confirmedCases confirmed cases and $deathCases $death in Nigeria.\n";

        CacheInfo::create([
            "info_date" => \Carbon\Carbon::now(),
            "info" => $conversation
        ]);

        return $conversation;
    }

    public function fetchConfirmedInfo()
    {
        $response = Http::get(
            "https://api.covid19api.com/country/nigeria/status/confirmed"
        );

        $latest = collect($response->json())->last();
        $date = \Carbon\Carbon::parse($latest["Date"])->format("jS F Y");

        return [$latest["Cases"], $date];
    }

    public function fetchDeathInfo()
    {
        $response = Http::get(
            "https://api.covid19api.com/country/nigeria/status/deaths"
        );

        $latest = collect($response->json())->last();

        return $latest["Cases"];
    }
}