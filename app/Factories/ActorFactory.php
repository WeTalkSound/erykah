<?php

namespace App\Factories;

use App\Contracts\Actor;
use App\Models\Converser;
use App\Actors\EmailActor;
use App\Actors\SaluteActor;
use App\Actors\BadResponseActor;
use App\Actors\CollectDataActor;
use App\Actors\InfoKeyWordActor;
use App\Actors\EvaluateKeywordActor;

abstract class ActorFactory
{

    /**
     * Actor
     * @var App\Contracts\Actor;
     */
    protected $actor;

    /**
     * Converser
     * @var App\Models\Converser
     */
    protected $converser;

    /**
     * Construct
     */
    public function __construct($identifier = null, $message = null)
    {
        $this->resolveConverser($identifier);
        $this->actor = $this->resolveActor($message);
    }

    /**
     * Make Actor
     */
    abstract public static function make(): Actor;

    /**
     * Resolve Converser
     * @param $converser
     * @return void
     */
    protected function resolveConverser(?string $identifier): void
    {
        $this->converser = Converser::firstOrCreate([
            'identifier' => $identifier
        ]);
    }

    /**
     * Resolve Actor
     * @param $message
     * 
     * @return App\Contracts\Actor
     */
    protected function resolveActor(?string $message): Actor
    {
        $message = $this->normalizeMessage($message);

        $actors = $this->getActors();

        foreach ($actors as $actor) {

            if ($actor::shouldTalk($this->converser, $message)) 
                return new $actor($this->converser, $message);
        }
        return new \App\Actors\SaluteActor($this->converser, $message);
    }

    /**
     * Trim and lower case the message
     */
    protected function normalizeMessage(?string $message): string
    {
        return trim(strtolower($message));
    }

    /**
     * Return resolved actor
     */
    public function actor(): Actor
    {
        return $this->actor;
    }

    /**
     * Get available actors
     * @return array
     */
    protected function getActors(): array
    {
        return [
            EmailActor::class,
            SaluteActor::class,
        ];
    }
}