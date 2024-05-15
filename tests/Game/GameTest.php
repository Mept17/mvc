<?php

namespace App\Tests\Game;

use PHPUnit\Framework\TestCase;
use App\Game\Game;
use App\Card\Deck;
use App\Card\Card;

/**
 * Test class for the Game class.
 */
class GameTest extends TestCase
{
    /**
     * Test to verify if initial scores of player and bank are set to 0.
     */
    public function testInitialScores(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $this->assertEquals(0, $game->getPlayerScore());
        $this->assertEquals(0, $game->getBankScore());
    }

    /**
     * Test to verify if player can draw a card and increase their score.
     */
    public function testPlayerDrawCard(): void
    {
        $deck = new Deck([new Card('Hearts', 'Ace')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $this->assertCount(1, $game->getPlayerCards());
        $this->assertGreaterThan(0, $game->getPlayerScore());
    }

    /**
     * Test to verify if player wins when player's score exceeds bank's score.
     */
    public function testDetermineWinnerPlayerWins(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(20);
        $game->getBank()->addToScore(15);
        $this->assertEquals('Player', $game->determineWinner());
    }

    /**
     * Test to verify if bank wins when bank's score exceeds player's score.
     */
    public function testDetermineWinnerBankWins(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(15);
        $game->getBank()->addToScore(20);
        $this->assertEquals('Bank', $game->determineWinner());
    }

    /**
     * Test to verify if it's a tie when both player and bank have the same score.
     */
    public function testDetermineWinnerTie(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(18);
        $game->getBank()->addToScore(18);
        $this->assertEquals('Bank', $game->determineWinner());
    }

    /**
     * Test to verify if the game can be reset with scores set back to 0.
     */
    public function testResetGame(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(20);
        $game->getBank()->addToScore(18);
        $game->resetGame();
        $this->assertEquals(0, $game->getPlayerScore());
        $this->assertEquals(0, $game->getBankScore());
        $this->assertFalse($game->isGameOver());
    }

    /**
     * Test to verify if bank can draw a card and increase their score.
     */
    public function testBankDrawCard(): void
    {
        $deck = new Deck([new Card('Hearts', 'Ace'), new Card('Diamonds', 'King')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $game->bankDrawCard();
        $this->assertCount(1, $game->getBankCards());
        $this->assertGreaterThan(0, $game->getBankScore());
    }

    /**
     * Test to verify if player has drawn a card.
     */
    public function testIsPlayerDrawn(): void
    {
        $deck = new Deck([new Card('Hearts', 'Ace')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $this->assertTrue($game->isPlayerDrawn());
    }

    /**
     * Test to verify if the value of a Queen card is correct.
     */
    public function testQueenCardValue(): void
    {
        $deck = new Deck([new Card('Hearts', 'Queen')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $this->assertEquals(12, $game->getPlayerScore());
    }

    /**
     * Test to verify if the value of a Jack card is correct.
     */
    public function testJackCardValue(): void
    {
        $deck = new Deck([new Card('Hearts', 'Jack')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $this->assertEquals(11, $game->getPlayerScore());
    }

    /**
     * Test to verify if player wins when their score exceeds 21.
     */
    public function testPlayerWinsWhenPlayerScoreExceeds21(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(22);
        $this->assertEquals('Bank', $game->determineWinner());
    }

    /**
     * Test to verify if bank wins when their score exceeds 21.
     */
    public function testBankWinsWhenBankScoreExceeds21(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getBank()->addToScore(22);
        $this->assertEquals('Player', $game->determineWinner());
    }

    /**
     * Test to verify if player wins with a higher score than bank.
     */
    public function testPlayerWinsWithHigherScore(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(20);
        $game->getBank()->addToScore(18);
        $this->assertEquals('Player', $game->determineWinner());
    }

    /**
     * Test to verify if bank wins with a higher score than player.
     */
    public function testBankWinsWithHigherScore(): void
    {
        $deck = new Deck([]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(18);
        $game->getBank()->addToScore(20);
        $this->assertEquals('Bank', $game->determineWinner());
    }

    /**
     * Test to verify if game is over when player's score exceeds 21.
     */
    public function testPlayerDrawCardGameOver(): void
    {
        $deck = new Deck([new Card('Hearts', 'King')]);
        $game = new Game($deck);
        $game->getPlayer()->addToScore(20);
        $game->playerDrawCard();
        $this->assertTrue($game->isGameOver());
    }

    /**
     * Test to verify if numeric value of a card is calculated correctly.
     */
    public function testCalculateCardValueNumericValue(): void
    {
        $deck = new Deck([new Card('Hearts', '8')]);
        $game = new Game($deck);
        $game->playerDrawCard();
        $this->assertEquals(8, $game->getPlayerScore());
    }
}
