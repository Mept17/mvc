<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardGraphic;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Representerar en kortlek som innehåller spelkort.
 */
class Deck
{
    /**
     * @var array<Card>
     */
    private array $cards;

    /**
     * Skapar en ny kortlek med dom angivna korten.
     *
     * @param array<Card|CardGraphic> $cards En array som innehåller objekt av typen Card eller CardGraphic.
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    /**
     * Skapar en ny kortlek med standardvärden.
     *
     * @param string $cardType Typen av kort som ska skapas.
     * @return Deck En ny instans av kortleken.
     */
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

    /**
     * Blandar kortleken.
     *
     * @return void
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Hämtar alla kort i kortleken.
     *
     * @return array<Card|CardGraphic> Alla kort i kortleken.
     */
    public function getAllCards(): array
    {
        return $this->cards;
    }

    /**
     * Sorterar kortleken.
     *
     * @return void
     */
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

    /**
     * Kontrollerar om kortleken är sorterad.
     *
     * @return bool True om kortleken är sorterad, annars false.
     */
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

    /**
     * Drar ett specifikt kort från kortleken baserat på index.
     *
     * @param int $index Index för det kort som ska dras.
     * @return Card|null Det dragna kortet eller null om indexet är ogiltigt.
     */
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

    /**
     * Skapar en ny kortlek för dragning.
     *
     * @param string $cardType Typen av kort som ska skapas.
     * @return Deck En ny instans av kortleken.
     */
    public static function createDrawFromDeck(string $cardType): Deck
    {
        return self::createDeck($cardType);
    }
}
