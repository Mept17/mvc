<?php

namespace App\BlackJack;

use App\Card\CardGraphic;

/**
 * Representerar en dealer i Blackjack-spelet.
 */
class Dealer
{
    /**
     * @var CardGraphic[] En array med kort i dealerns hand.
     */
    private array $cards = [];
    private int $points = 0;

    /**
     * Lägg till ett kort till dealern.
     *
     * @param CardGraphic $card Det kort som ska läggas till.
     * @return void
     */
    public function addCard(CardGraphic $card): void
    {
        $this->cards[] = $card;
        $cardValue = $card->getValue();

        if (is_numeric($cardValue)) {
            $this->points += (int) $cardValue;
        }
    }

    /**
     * Hämtar alla kort i dealerns hand.
     *
     * @return CardGraphic[] Alla kort i dealerns hand.
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Hämta totala poängen för dealerns hand.
     *
     * @return int Totala poängen för dealerns hand.
     */
    public function getPoints(): int
    {
        return $this->points;
    }
}
