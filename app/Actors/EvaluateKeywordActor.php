<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Models\Converser;
use App\Models\Conversation;

class EvaluateKeywordActor extends Actor
{
    protected $screenQuestions = [
        "Do you have cough?" => [
            "yes" => 1, "no" => 0
        ],
        "Do you have cold?" => [
            "yes" => 1, "no" => 0,
        ],
        "Are you having diarrhea?" => [
            "yes" => 1, "no" => 0,
        ],
        "Do you have a sore throat?" => [
            "yes" => 1, "no" => 0,
        ],
        "Are you experiencing body aches?" => [
            "yes" => 1, "no" => 0,
        ],
        "Do you have a headache?" => [
            "yes" => 1, "no" => 0,
        ],
        "Do you have a fever?" => [
            "yes" => 1, "no" => 0,
        ],
        "Are you having difficulty breathing?" => [
            "yes" => 2, "no" => 0,
        ],
        "Are you experiencing fatigue?" => [
            "yes" => 2, "no" => 0,
        ],
        "Do you have a pre existing respiratory condition" => [
            "yes" => 2, "maybe" => 1, "no" => 0
        ],
        "Have you recently traveled during the past 30 days?" => [
            "yes" => 3, "maybe" => 2, "no" => 0,
        ],
        "Do you have a travel history to a COVID-19 infected area?" => [
            "yes" => 3, "maybe" => 2, "no" => 0,
        ],
        "Have you had direct contact with a COVID-19 suspected or confirmed case ?" => [
            "yes" => 3, "maybe" => 2, "no" => 0,
        ],
    ];

    protected $scoreResults = [
        2 => "You're at low risk. This maybe stress related. You are advised to isolate and observe your self. You can contact the NCDC on toll free: 0800970000 .Thank you",
        5 => "You're at low risk. Hydrate properly, maintain good hygiene, observe and re evaluate after 2 days. You can contact the NCDC on toll free: 0800970000 . Thank you",
        12 => "You're at medium risk. You may need to consult with a doctor. You can contact the NCDC on toll free: 0800970000 . Thank you"
    ];


    /**
     * should talk
     */
     public static function shouldTalk(Converser $converser, string $message): bool
     {
         return $message == "/evaluate" || $converser->conversation;
     }

     /**
     * Converse
     * @return string
     */
    public function talk(): string
    {
        return $this->converser->conversation && 
                $this->converser->conversation->question ? 
                $this->talkForConversation() :
                $this->startConversation();
    }

    /**
     * Check Answer
     */
    protected function talkForConversation()
    {
        $conversation = $this->converser->conversation;

        return $conversation->has_next_question ? 
                $this->giveNextQuestion($conversation) : 
                $this->giveResult($conversation);
    }

    protected function giveResult($conversation)
    {
        [$current, $next] = $this->getIndicies($conversation->question);
        $options = $this->getOptions($current);

        $value = $this->calculateValue($options);

        foreach ($this->scoreResults as $point => $message) {
            if (in_array($value, range(0, $point))) {
                return $this->finish($message);
            }
        }

        $conversation->update([
            "collect_data" => true
        ]);

        $conversation->refresh();

        $convo = "You're at high risk of infection. Please provide us with the following information so as to escalate your risk status to the NCDC \n";
        $convo .= array_keys($conversation->data ?? [])[0] ?? "";

        return $convo;

    }

    protected function finish($message)
    {
        $this->converser->conversation->delete();

        return $message;
    }

    protected function giveNextQuestion($conversation)
    {
        [$current, $next] = $this->getIndicies($conversation->question);
        $options = $this->getOptions($current);

        $conversation = $this->saveOngoingConvo($next, $options);

        return $this->buildOngoingConvo($conversation);

    }

    protected function startConversation()
    {
        $conversation = $this->saveOngoingConvo(0);

        return $this->buildOngoingConvo($conversation);
    }

    protected function saveOngoingConvo($index, $options = [])
    {
        $question = $this->getQuestion($index);
        $options = $this->getOptions($index);
        $hasNextQuestion = $this->hasNextQuestion($index);
        $value = $this->calculateValue($options);

        return Conversation::updateOrCreate(["converser_id" => $this->converser->id],
                [
                    "actor" => static::class,
                    "question" => $question,
                    "value" => $value,
                    "has_next_question" => $hasNextQuestion,
                    "expected_answers" => $options
                ]);
    }

    protected function buildOngoingConvo($conversation)
    {
        $conversation->refresh();

        $convo = $conversation->question . "\n";
        $convo .= "reply with: \n";

        $options = array_keys($conversation->expected_answers);
        foreach ($options as $option) {
            $convo .= $option . "\n";
        }

        return $convo;

    }

    protected function calculateValue($options)
    {
        if ($conversation = $this->converser->conversation) {
            return $conversation->value + ($options[$this->message] ?? 0);
        }
        return 0;
        
    }

    /**
     * Perfom if gamer answers correctly
     */
    protected function hasNextQuestion($index)
    {
        return (bool)$this->getQuestion($index + 1);
    }

    /**
     * Perform if gamer has answers previously
     */
    public function getOptions($index)
    {
        return $this->screenQuestions()->values()->get($index);
    }

    public function getQuestion($index)
    {
        return $this->screenQuestions()->keys()->get($index);
    }

    public function getIndicies($question)
    {
        $this->screenQuestions()->keys()->map(
            function ($ques, $key) use (&$next, &$current, $question){
                if ($question == $ques) {
                    $next = $key + 1;
                    $current = $key;
                }
        });

        return [$current, $next];
    }

    /**
     * Generate next question
     */
    protected function screenQuestions()
    {
        return collect($this->screenQuestions);
    }
}