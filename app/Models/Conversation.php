<?php

namespace App\Models;

use App\Traits\HasMeta;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasMeta;

    protected $fillable = ["converser_id", "actor"];

    protected $casts = [
        "meta" => "array"
    ];

    public function meta()
    {
        return [
            "question" => null,
            "value" => 0,
            "has_next_question" => true,
            "expected_answers" => [],
            "collect_data" => false,
            "data" => [
                "What's your name?" => null,
                "What's your phone number?" => null,
                "What's your address?" => null
            ]
        ];
    }
}
