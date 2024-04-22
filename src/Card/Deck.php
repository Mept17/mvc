<?php

namespace App\Card;

use App\Card\Card;

use Symfony\Component\HttpFoundation\Session\Session;

class Deck
{
    private array $cards;

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
                if ($cardType === 'Card') {
                    $cards[] = new Card($suit, $value);
                } elseif ($cardType === 'CardGraphic') {
                    $cardGraphic = new CardGraphic($suit, $value);
                    $graphic = $cardGraphic->getGraphic();
                    $cards[] = new CardGraphic($suit, $value, $graphic);
                }
            }
        }

        $deck = new self($cards);

        $session = new Session();
        $session->set('deck', $deck);

        return new self($cards);
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

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
        $values = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $cards = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                if ($cardType === 'Card') {
                    $cards[] = new Card($suit, $value);
                }
            }
        }
        return new self($cards);
    }
}
