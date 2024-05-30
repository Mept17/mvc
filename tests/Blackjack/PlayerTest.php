<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\Player;
use App\Card\CardGraphic;

/**
 * Enhetstester för klassen Player.
 */
class PlayerTest extends TestCase
{
    private Player $player;

    /**
     * Ställer in miljön före varje test.
     */
    protected function setUp(): void
    {
        $this->player = new Player('John', 100);
    }

    /**
     * Testar att få spelarens namn.
     */
    public function testGetName(): void
    {
        $this->assertEquals('John', $this->player->getName());
    }

    /**
     * Testar att få spelarens poäng.
     */
    public function testGetScore(): void
    {
        $this->assertEquals(0, $this->player->getScore());
    }

    /**
     * Testar att lägga till poäng till spelaren.
     */
    public function testAddToScore(): void
    {
        $this->player->addToScore(5);
        $this->assertEquals(5, $this->player->getScore());
    }

    /**
     * Testar att återställa spelarens poäng.
     */
    public function testResetScore(): void
    {
        $this->player->addToScore(5);
        $this->player->resetScore();
        $this->assertEquals(0, $this->player->getScore());
    }

    /**
     * Testar att lägga till ett kort till spelaren.
     */
    public function testAddCard(): void
    {
        $card = new CardGraphic('Hearts', '2');
        $this->player->addCard($card);
        $this->assertCount(1, $this->player->getCards());
    }

    /**
     * Testar att hämta spelarens kort.
     */
    public function testGetCards(): void
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

    /**
     * Testar att få spelarens pengar.
     */
    public function testGetMoney(): void
    {
        $this->assertEquals(100, $this->player->getMoney());
    }

    /**
     * Testar att justera spelarens pengar.
     */
    public function testAdjustMoney(): void
    {
        $this->player->adjustMoney(50);
        $this->assertEquals(150, $this->player->getMoney());
    }

    /**
     * Testar om spelaren är "bust".
     */
    public function testIsBust(): void
    {
        $this->assertFalse($this->player->isBust());
    }

    /**
     * Testar att sätta spelarens "bust" status.
     */
    public function testSetBust(): void
    {
        $this->player->setBust(true);
        $this->assertTrue($this->player->isBust());
    }
}
