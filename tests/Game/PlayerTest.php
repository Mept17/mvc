<?php

namespace App\Tests\Game;

use PHPUnit\Framework\TestCase;
use App\Game\Player;
use App\Card\Card;

/**
 * Test class for the Player class.
 */
class PlayerTest extends TestCase
{
    /**
     * Test to verify if initial score of player is set to 0.
     */
    public function testInitialScore(): void
    {
        $player = new Player();
        $this->assertEquals(0, $player->getScore());
    }

    /**
     * Test to verify if score is correctly incremented when adding points.
     */
    public function testAddToScore(): void
    {
        $player = new Player();
        $player->addToScore(10);
        $this->assertEquals(10, $player->getScore());
    }

    /**
     * Test to verify if player's score is reset to 0.
     */
    public function testResetScore(): void
    {
        $player = new Player();
        $player->addToScore(10);
        $player->resetScore();
        $this->assertEquals(0, $player->getScore());
    }

    /**
     * Test to verify if a card is added to the player's hand.
     */
    public function testAddCard(): void
    {
        $player = new Player();
        $card = new Card('Hearts', 'Ace');
        $player->addCard($card);
        $this->assertCount(1, $player->getCards());
    }
}
