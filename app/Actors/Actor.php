<?php

namespace App\Actors;

use App\Contracts\Actor as ActorContract;
use App\Models\Converser;

abstract class Actor implements ActorContract
{
    /**
     * @var Converser
     */
    protected $converser;

    /**
     * @var string
     */
    protected $message;

    public function __construct($converser, $message)
    {
        $this->converser = $converser;
        $this->message = $message;
    }

    /**
     * Call Actor from within an actor
     * @param string $actor
     * @param mixed $data
     * @return string $convo
     */
    protected function call(string $actor)
    {
        $actor = new $actor($this->converser, $this->message);
        return $actor->talk();
    }

    public function getConverser(): Converser
    {
        return $this->converser;
    }

    public function getMessage()
    {
        return $this->message;
    }
}