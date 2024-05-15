<?php

namespace App\Game;

use App\Card\Deck;
use App\Game\Player;
use App\Card\CardGraphic;

class Game
{
    private Deck $deck;
    private Player $player;
    private Player $bank;
    private bool $gameOver;
    private bool $playerDrawn;

    public function __construct(Deck $deck)
    {
        $this->deck = $deck;
        $this->player = new Player();
        $this->bank = new Player();
        $this->gameOver = false;
        $this->playerDrawn = false;
    }

    public function getPlayerScore(): int
    {
        return $this->player->getScore();
    }

    public function getBankScore(): int
    {
        return $this->bank->getScore();
    }

    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    public function playerDrawCard(): void
    {
        if (!$this->gameOver) {
            $card = $this->deck->drawSpecificCard(0);
            if ($card !== null) { // Kontrollera om $card är null eller inte
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

    public function bankDrawCard(): void
    {
        if (!$this->gameOver && $this->playerDrawn) {
            while ($this->bank->getScore() < 17) {
                $card = $this->deck->drawSpecificCard(0);
                if ($card !== null) { // Kontrollera om $card är null eller inte
                    $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                    $value = $this->calculateCardValue($card);
                    $this->bank->addToScore($value);
                    $this->bank->addCard($graphicCard);
                } else {
                    break; // Om $card är null, bryt loopen
                }
            }
            $this->gameOver = true;
        }
    }

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

    public function resetGame(): void
    {
        $this->player->resetScore();
        $this->bank->resetScore();
        $this->deck->shuffle();
        $this->gameOver = false;
    }

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
     * @return \App\Card\Card[]
     */
    public function getPlayerCards(): array
    {
        return $this->player->getCards();
    }

    /**
     * @return \App\Card\Card[]
     */
    public function getBankCards(): array
    {
        return $this->bank->getCards();
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function isPlayerDrawn(): bool
    {
        return $this->playerDrawn;
    }

    public function getBank(): Player
    {
        return $this->bank;
    }
}
