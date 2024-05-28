<?php

namespace App\Card;

/**
 * Representerar ett spelkort med grafisk representation.
 */
class CardGraphic extends Card
{
    /**
     * @var array<string, array<string>>
     */
    private array $representation = [
        'Hearts' => ['Ace' => '🂱', '2' => '🂲', '3' => '🂳', '4' => '🂴', '5' => '🂵', '6' => '🂶', '7' => '🂷', '8' => '🂸', '9' => '🂹', '10' => '🂺', 'Jack' => '🂻', 'Queen' => '🂽', 'King' => '🂾'],
        'Diamonds' => ['Ace' => '🃁', '2' => '🃂', '3' => '🃃', '4' => '🃄', '5' => '🃅', '6' => '🃆', '7' => '🃇', '8' => '🃈', '9' => '🃉', '10' => '🃊', 'Jack' => '🃋', 'Queen' => '🃍', 'King' => '🃎'],
        'Clubs' => ['Ace' => '🃑', '2' => '🃒', '3' => '🃓', '4' => '🃔', '5' => '🃕', '6' => '🃖', '7' => '🃗', '8' => '🃘', '9' => '🃙', '10' => '🃚', 'Jack' => '🃛', 'Queen' => '🃝', 'King' => '🃞'],
        'Spades' => ['Ace' => '🂡', '2' => '🂢', '3' => '🂣', '4' => '🂤', '5' => '🂥', '6' => '🂦', '7' => '🂧', '8' => '🂨', '9' => '🂩', '10' => '🂪', 'Jack' => '🂫', 'Queen' => '🂭', 'King' => '🂮'],
    ];

    /**
     * Skapar ett spelkort med grafisk representation.
     *
     * @param string $suit Färgen på kortet.
     * @param string $value Värdet på kortet.
     */
    public function __construct(string $suit, string $value)
    {
        parent::__construct($suit, $value);
    }

    /**
     * Hämtar den grafiska representationen av kortet.
     *
     * @return string Den grafiska representationen av kortet.
     */
    public function getGraphic(): string
    {
        return $this->representation[$this->getSuit()][$this->getValue()];
    }

    /**
     * Hämtar färgen på kortet.
     *
     * @return string Färgen på kortet (röd eller svart).
     */
    public function getSuitColor(): string
    {
        return ($this->getSuit() === 'Hearts' || $this->getSuit() === 'Diamonds') ? 'red' : 'black';
    }

    /**
     * Returnera den grafiska representationen av kortet som en sträng.
     *
     * @return string Den grafiska representationen av kortet.
     */
    public function __toString(): string
    {
        return $this->getGraphic();
    }
}
