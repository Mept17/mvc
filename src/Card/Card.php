<?php

namespace App\Card;

/**
 * Representerar ett spelkort.
 */
class Card
{
    private string $suit;
    private string $value;

    /**
     * Skapar ett spelkort med den angivna färgen och värdet.
     *
     * @param string $suit Färgen på kortet.
     * @param string $value Värdet på kortet.
     */
    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * Hämtar färgen på kortet.
     *
     * @return string Färgen på kortet.
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Hämtar värdet på kortet.
     *
     * @return string Värdet på kortet.
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
