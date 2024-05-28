<?php

namespace App\BlackJack;

use App\Card\CardGraphic;

/**
 * Representerar en spelare i Blackjack-spelet.
 */
class Player
{
    private string $name;
    private int $score;
    /**
     * @var CardGraphic[] En array med kort i spelarens hand.
     */
    private array $cards;
    private int $money;
    private bool $bust;

    /**
     * Skapa en ny spelare med namn och startpengar.
     *
     * @param string $name Spelarens namn.
     * @param int $money Startpengar för spelaren.
     */
    public function __construct(string $name, int $money = 0)
    {
        $this->name = $name;
        $this->score = 0;
        $this->cards = [];
        $this->money = $money;
        $this->bust = false; // Initialize as not bust
    }

    /**
     * Hämtar spelarens namn.
     *
     * @return string Spelarens namn.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Hämtar spelarens aktuella poäng.
     *
     * @return int Spelarens poäng.
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Lägg till poäng till spelarens totala poäng.
     *
     * @param int $points Poäng att lägga till.
     * @return void
     */
    public function addToScore(int $points): void
    {
        $this->score += $points;
    }

    /**
     * Återställ spelarens poäng och hand till standardvärden.
     *
     * @return void
     */
    public function resetScore(): void
    {
        $this->score = 0;
        $this->cards = [];
        $this->bust = false; // Reset bust status
    }

    /**
     * Lägg till ett kort till spelarens hand.
     *
     * @param CardGraphic $card Kortet som ska läggas till.
     * @return void
     */
    public function addCard(CardGraphic $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Hämtar alla kort i spelarens hand.
     *
     * @return CardGraphic[] Alla kort i spelarens hand.
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Hämtar spelarens aktuella pengar.
     *
     * @return int Spelarens pengar.
     */
    public function getMoney(): int
    {
        return $this->money;
    }

    /**
     * Justerar spelarens pengar med beloppet.
     *
     * @param int $amount Beloppet att justera spelarens pengar med.
     * @return void
     */
    public function adjustMoney(int $amount): void
    {
        $this->money += $amount;
    }

    /**
     * Kontrollera om spelaren är "busted".
     *
     * @return bool True om spelaren är "busted", annars false.
     */
    public function isBust(): bool
    {
        return $this->bust;
    }

    /**
     * Ange om spelaren är "busted" eller inte.
     *
     * @param bool $bust True om spelaren är "busted", annars false.
     * @return void
     */
    public function setBust(bool $bust): void
    {
        $this->bust = $bust;
    }
}
