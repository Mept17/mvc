<?php

namespace App\Game;

use App\Card\Card;

class Player
{
    private int $score;
    /**
     * @var Card[]
     */
    private array $cards = [];

    public function __construct()
    {
        $this->score = 0;
        $this->cards = [];
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function addToScore(int $value): void
    {
        $this->score += $value;
    }

    public function resetScore(): void
    {
        $this->score = 0;
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
