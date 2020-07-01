<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Converser extends Model
{
    protected $guarded = [];

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }
}
