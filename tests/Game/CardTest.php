<?php

namespace App\Tests\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Card;

/**
 * Test class for the Card class.
 */
class CardTest extends TestCase
{
    /**
     * Test to verify if getSuit() method returns the correct suit of the card.
     */
    public function testGetSuit(): void
    {
        // Creating a card with Hearts suit and Ace value
        $card = new Card('Hearts', 'Ace');
        // Asserting that the suit retrieved from the card is 'Hearts'
        $this->assertEquals('Hearts', $card->getSuit());
    }

    /**
     * Test to verify if getValue() method returns the correct value of the card.
     */
    public function testGetValue(): void
    {
        // Creating a card with Hearts suit and Ace value
        $card = new Card('Hearts', 'Ace');
        // Asserting that the value retrieved from the card is 'Ace'
        $this->assertEquals('Ace', $card->getValue());
    }
}
