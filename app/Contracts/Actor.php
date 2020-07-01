<?php

namespace App\Contracts;

use App\Models\Converser;

interface Actor
{
    /**
     * Converse
     * 
     * @return string|array
     */
    public function talk();

    /**
     * Should Talk
     * @param App\Models\Converser $converser
     * @param string $message
     * 
     * @return bool
     */
    public static function shouldTalk(Converser $converser, string $message): bool;

    /**
     * Returns the Actor's Converser
     * @return Converser
     */
    public function getConverser(): Converser;
}