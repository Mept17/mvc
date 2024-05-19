<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardGraphic;

use Symfony\Component\HttpFoundation\Session\Session;

class Deck
{
    /**
     * @var array<Card>
     */
    private array $cards;

    /**
     * @param array<Card|CardGraphic> $cards An array containing objects of type Card or CardGraphic.
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    public static function createDeck(string $cardType): Deck
    {
        $values = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $cards = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $card = ($cardType === 'CardGraphic') ? new CardGraphic($suit, $value) : new Card($suit, $value);
                $cards[] = $card;
            }
        }

        $deck = new self($cards);

        $session = new Session();
        $session->set('deck', $deck);

        return $deck;
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * @return array<Card|CardGraphic>
     */
    public function getAllCards(): array
    {
        return $this->cards;
    }

    public function sortDeck(): void
    {
        usort($this->cards, function ($a, $b) {
            $valuesOrder = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
            $suitsOrder = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];

            $suitComparison = array_search($a->getSuit(), $suitsOrder, true) - array_search($b->getSuit(), $suitsOrder, true);
            if ($suitComparison !== 0) {
                return $suitComparison;
            }

            $valueComparison = array_search($a->getValue(), $valuesOrder, true) - array_search($b->getValue(), $valuesOrder, true);
            return $valueComparison;
        });
    }

    public function isSorted(): bool
    {
        $valuesOrder = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
        $suitsOrder = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];

        foreach ($this->cards as $index => $card) {
            $expectedValue = $valuesOrder[$index % 13];
            $expectedSuit = $suitsOrder[(int)($index / 13)];

            if ($card->getValue() !== $expectedValue || $card->getSuit() !== $expectedSuit) {
                return false;
            }
        }

        return true;
    }

    public function drawSpecificCard(int $index): ?Card
    {
        if (isset($this->cards[$index])) {
            $drawnCard = $this->cards[$index];
            unset($this->cards[$index]);
            $this->cards = array_values($this->cards);
            return $drawnCard;
        }
        return null;
    }

    public static function createDrawFromDeck(string $cardType): Deck
    {
        return self::createDeck($cardType);
    }
}
