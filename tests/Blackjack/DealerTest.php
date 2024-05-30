<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\Dealer;
use App\Card\CardGraphic;

/**
 * Enhetstester för klassen Dealer.
 */
class DealerTest extends TestCase
{
    private Dealer $dealer;

    /**
     * Ställer in miljön före varje test.
     */
    protected function setUp(): void
    {
        $this->dealer = new Dealer();
    }

    /**
     * Testar att lägga till ett kort till dealern.
     */
    public function testAddCard(): void
    {
        $card = new CardGraphic('Hearts', '2');
        $this->dealer->addCard($card);
        $this->assertCount(1, $this->dealer->getCards());
    }

    /**
     * Testar att hämta dealerns kort.
     */
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

    /**
     * Testar att få poäng när dealern inte har några kort.
     */
    public function testGetPointsNoCards(): void
    {
        $this->assertEquals(0, $this->dealer->getPoints());
    }

    /**
     * Testar att få poäng när dealern har kort med nummer.
     */
    public function testGetPointsWithNumericCards(): void
    {
        $this->dealer->addCard(new CardGraphic('Hearts', '2'));
        $this->dealer->addCard(new CardGraphic('Diamonds', '3'));
        $this->assertEquals(5, $this->dealer->getPoints());
    }

    /**
     * Testar att få poäng när dealern har kort utan nummer.
     */
    public function testGetPointsWithNonNumericCards(): void
    {
        $this->dealer->addCard(new CardGraphic('Hearts', 'King'));
        $this->dealer->addCard(new CardGraphic('Diamonds', 'Ace'));
        $this->assertEquals(0, $this->dealer->getPoints());
    }
}
