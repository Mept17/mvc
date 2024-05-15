<?php

namespace App\Game;

use App\Card\Card;

/**
 * Represents a player in the game.
 */
class Player
{
    private int $score;
    /**
     * @var Card[]
     */
    private array $cards = [];

    /**
     * Constructs a new Player instance.
     */
    public function __construct()
    {
        $this->score = 0;
        $this->cards = [];
    }

    /**
     * Gets the current score of the player.
     *
     * @return int The player's score.
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Adds a value to the player's score.
     *
     * @param int $value The value to add to the player's score.
     */
    public function addToScore(int $value): void
    {
        $this->score += $value;
    }

    /**
     * Resets the player's score to zero.
     */
    public function resetScore(): void
    {
        $this->score = 0;
    }

    /**
     * Adds a card to the player's hand.
     *
     * @param Card $card The card to add to the player's hand.
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Gets the cards held by the player.
     *
     * @return Card[] An array of cards held by the player.
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
