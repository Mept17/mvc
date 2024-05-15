<?php

namespace App\Game;

use App\Card\Deck;
use App\Game\Player;
use App\Card\CardGraphic;

/**
 * Represents a game of the card game 21.
 */
class Game
{
    private Deck $deck;
    private Player $player;
    private Player $bank;
    private bool $gameOver;
    private bool $playerDrawn;

    /**
     * Constructs a new Game instance.
     *
     * @param Deck $deck The deck of cards to be used in the game.
     */
    public function __construct(Deck $deck)
    {
        $this->deck = $deck;
        $this->player = new Player();
        $this->bank = new Player();
        $this->gameOver = false;
        $this->playerDrawn = false;
    }

    /**
     * Gets the current score of the player.
     *
     * @return int The player's score.
     */
    public function getPlayerScore(): int
    {
        return $this->player->getScore();
    }

    /**
     * Gets the current score of the bank.
     *
     * @return int The bank's score.
     */
    public function getBankScore(): int
    {
        return $this->bank->getScore();
    }

    /**
     * Checks if the game is over.
     *
     * @return bool True if the game is over, otherwise false.
     */
    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    /**
     * Handles player drawing a card.
     */
    public function playerDrawCard(): void
    {
        if (!$this->gameOver) {
            $card = $this->deck->drawSpecificCard(0);
            if ($card !== null) {
                $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                $value = $this->calculateCardValue($card);
                $this->player->addToScore($value);
                $this->player->addCard($graphicCard);
                if ($this->player->getScore() > 21) {
                    $this->gameOver = true;
                }
                $this->playerDrawn = true;
            }
        }
    }

    /**
     * Handles bank drawing cards based on game rules.
     */
    public function bankDrawCard(): void
    {
        if (!$this->gameOver && $this->playerDrawn) {
            while ($this->bank->getScore() < 17) {
                $card = $this->deck->drawSpecificCard(0);
                if ($card !== null) {
                    $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                    $value = $this->calculateCardValue($card);
                    $this->bank->addToScore($value);
                    $this->bank->addCard($graphicCard);
                } else {
                    break;
                }
            }
            $this->gameOver = true;
        }
    }

    /**
     * Calculates the value of a card.
     *
     * @param \App\Card\Card $card The card to calculate the value for.
     *
     * @return int The value of the card.
     */
    public function calculateCardValue(\App\Card\Card $card): int
    {
        $value = $card->getValue();
        if ($value === 'Ace') {
            $scoreWithoutAce = $this->player->getScore() + $this->bank->getScore();
            return ($scoreWithoutAce + 14 > 21) ? 1 : 14;
        } elseif ($value === 'King') {
            return 13;
        } elseif ($value === 'Queen') {
            return 12;
        } elseif ($value === 'Jack') {
            return 11;
        }
        return (int)$value;
    }

    /**
     * Resets the game to its initial state.
     */
    public function resetGame(): void
    {
        $this->player->resetScore();
        $this->bank->resetScore();
        $this->deck->shuffle();
        $this->gameOver = false;
    }

    /**
     * Determines the winner of the game.
     *
     * @return string The winner of the game ('Player' or 'Bank').
     */
    public function determineWinner(): string
    {
        if ($this->player->getScore() > 21) {
            return 'Bank';
        } elseif ($this->bank->getScore() > 21) {
            return 'Player';
        }

        if ($this->player->getScore() > $this->bank->getScore()) {
            return 'Player';
        } elseif ($this->player->getScore() < $this->bank->getScore()) {
            return 'Bank';
        } else {
            return 'Bank';
        }
    }

    /**
     * Gets the cards held by the player.
     *
     * @return \App\Card\Card[] An array of cards held by the player.
     */
    public function getPlayerCards(): array
    {
        return $this->player->getCards();
    }

    /**
     * Gets the cards held by the bank.
     *
     * @return \App\Card\Card[] An array of cards held by the bank.
     */
    public function getBankCards(): array
    {
        return $this->bank->getCards();
    }

    /**
     * Gets the player instance.
     *
     * @return Player The player instance.
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Checks if the player has drawn a card.
     *
     * @return bool True if the player has drawn a card, otherwise false.
     */
    public function isPlayerDrawn(): bool
    {
        return $this->playerDrawn;
    }

    /**
     * Gets the bank instance.
     *
     * @return Player The bank instance.
     */
    public function getBank(): Player
    {
        return $this->bank;
    }
}
