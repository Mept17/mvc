<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\Dealer;
use App\Card\CardGraphic;

class DealerTest extends TestCase
{
    private Dealer $dealer;

    protected function setUp(): void
    {
        $this->dealer = new Dealer();
    }

    public function testAddCard(): void
    {
        $card = new CardGraphic('Hearts', '2');
        $this->dealer->addCard($card);
        $this->assertCount(1, $this->dealer->getCards());
    }

    public function testGetCards(): void
    {
        $card1 = new CardGraphic('Hearts', '2');
        $card2 = new CardGraphic('Diamonds', 'Ace');
        $this->dealer->addCard($card1);
        $this->dealer->addCard($card2);
        $cards = $this->dealer->getCards();
        $this->assertCount(2, $cards);
        $this->assertSame($card1, $cards[0]);
        $this->assertSame($card2, $cards[1]);
    }

    public function testGetPointsNoCards(): void
    {
        $this->assertEquals(0, $this->dealer->getPoints());
    }

    public function testGetPointsWithNumericCards(): void
    {
        $this->dealer->addCard(new CardGraphic('Hearts', '2'));
        $this->dealer->addCard(new CardGraphic('Diamonds', '3'));
        $this->assertEquals(5, $this->dealer->getPoints());
    }

    public function testGetPointsWithNonNumericCards(): void
    {
        $this->dealer->addCard(new CardGraphic('Hearts', 'King'));
        $this->dealer->addCard(new CardGraphic('Diamonds', 'Ace'));
        $this->assertEquals(0, $this->dealer->getPoints());
    }
}
