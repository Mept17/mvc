<?php

namespace App\Card;

class CardGraphic extends Card
{
    private $representation = [
        'Hearts' => ['Ace' => '🂱', '2' => '🂲', '3' => '🂳', '4' => '🂴', '5' => '🂵', '6' => '🂶', '7' => '🂷', '8' => '🂸', '9' => '🂹', '10' => '🂺', 'Jack' => '🂻', 'Queen' => '🂽', 'King' => '🂾'],
        'Diamonds' => ['Ace' => '🃁', '2' => '🃂', '3' => '🃃', '4' => '🃄', '5' => '🃅', '6' => '🃆', '7' => '🃇', '8' => '🃈', '9' => '🃉', '10' => '🃊', 'Jack' => '🃋', 'Queen' => '🃍', 'King' => '🃎'],
        'Clubs' => ['Ace' => '🃑', '2' => '🃒', '3' => '🃓', '4' => '🃔', '5' => '🃕', '6' => '🃖', '7' => '🃗', '8' => '🃘', '9' => '🃙', '10' => '🃚', 'Jack' => '🃛', 'Queen' => '🃝', 'King' => '🃞'],
        'Spades' => ['Ace' => '🂡', '2' => '🂢', '3' => '🂣', '4' => '🂤', '5' => '🂥', '6' => '🂦', '7' => '🂧', '8' => '🂨', '9' => '🂩', '10' => '🂪', 'Jack' => '🂫', 'Queen' => '🂭', 'King' => '🂮'],
    ];

    public function __construct(string $suit, string $value)
    {
        parent::__construct($suit, $value);
    }

    public function getGraphic(): string
    {
        return $this->representation[$this->getSuit()][$this->getValue()];
    }

    public function getSuitColor(): string
    {
        return ($this->getSuit() === 'Hearts' || $this->getSuit() === 'Diamonds') ? 'red' : 'black';
    }
}
