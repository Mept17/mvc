<?php

namespace App\Tests\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Deck;
use App\Card\Card;
use App\Card\CardGraphic;

session_start();

/**
 * Test class for the Deck class.
 */
class DeckTest extends TestCase
{
    /**
     * Test to verify if createDeck() method creates a deck with 52 cards.
     */
    public function testCreateDeck(): void
    {
        $deck = Deck::createDeck('Card');
        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertCount(52, $deck->getAllCards());
    }

    /**
     * Test to verify if shuffle() method shuffles the deck.
     */
    public function testShuffle(): void
    {
        $deck = Deck::createDeck('Card');
        $originalOrder = $deck->getAllCards();

        $deck->shuffle();
        $shuffledOrder = $deck->getAllCards();

        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }

    /**
     * Test to verify if drawSpecificCard() method returns a card at a specific index.
     */
    public function testDrawSpecificCard(): void
    {
        $deck = Deck::createDeck('Card');
        $drawnCard = $deck->drawSpecificCard(0);

        $this->assertInstanceOf(Card::class, $drawnCard);
    }

    /**
     * Test to verify if drawSpecificCard() method returns null when index is out of bounds.
     */
    public function testDrawSpecificCardWhenIndexOutOfBounds(): void
    {
        $deck = Deck::createDeck('Card');
        $drawnCard = $deck->drawSpecificCard(60);

        $this->assertNull($drawnCard);
    }

    /**
     * Test to verify if sortDeck() method sorts the deck.
     */
    public function testIsSorted(): void
    {
        $deck = Deck::createDeck('Card');
        $deck->sortDeck();

        $this->assertTrue($deck->isSorted());
    }

    /**
     * Test to verify if isSorted() method returns false when the deck is not sorted.
     */
    public function testIsSortedWhenDeckNotSorted(): void
    {
        $deck = Deck::createDeck('Card');
        $deck->shuffle();
        $this->assertFalse($deck->isSorted());
    }

    /**
     * Test to verify if createDrawFromDeck() method creates a deck with 52 cards.
     */
    public function testCreateDrawFromDeck(): void
    {
        $deck = Deck::createDrawFromDeck('Card');
        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertCount(52, $deck->getAllCards());
    }

    /**
     * Test to verify if createDeck() method creates a deck with CardGraphic instances.
     */
    public function testCreateDeckWithCardGraphic(): void
    {
        $deck = Deck::createDeck('CardGraphic');
        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertCount(52, $deck->getAllCards());

        foreach ($deck->getAllCards() as $card) {
            $this->assertInstanceOf(CardGraphic::class, $card);
        }
    }
}
