<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\Player;
use App\Card\CardGraphic;

class PlayerTest extends TestCase
{
    private $player;

    protected function setUp(): void
    {
        $this->player = new Player('John', 100); // Assuming starting money is 100
    }

    public function testGetName()
    {
        $this->assertEquals('John', $this->player->getName());
    }

    public function testGetScore()
    {
        $this->assertEquals(0, $this->player->getScore());
    }

    public function testAddToScore()
    {
        $this->player->addToScore(5);
        $this->assertEquals(5, $this->player->getScore());
    }

    public function testResetScore()
    {
        $this->player->addToScore(5);
        $this->player->resetScore();
        $this->assertEquals(0, $this->player->getScore());
    }

    public function testAddCard()
    {
        $card = new CardGraphic('Hearts', '2');
        $this->player->addCard($card);
        $this->assertCount(1, $this->player->getCards());
    }

    public function testGetCards()
    {
        $card1 = new CardGraphic('Hearts', '2');
        $card2 = new CardGraphic('Diamonds', 'Ace');
        $this->player->addCard($card1);
        $this->player->addCard($card2);
        $cards = $this->player->getCards();
        $this->assertCount(2, $cards);
        $this->assertSame($card1, $cards[0]);
        $this->assertSame($card2, $cards[1]);
    }

    public function testGetMoney()
    {
        $this->assertEquals(100, $this->player->getMoney());
    }

    public function testAdjustMoney()
    {
        $this->player->adjustMoney(50);
        $this->assertEquals(150, $this->player->getMoney());
    }

    public function testIsBust()
    {
        $this->assertFalse($this->player->isBust());
    }

    public function testSetBust()
    {
        $this->player->setBust(true);
        $this->assertTrue($this->player->isBust());
    }
}