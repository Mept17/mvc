<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\BlackJackService;
use App\BlackJack\Player;
use App\Card\Deck;
use App\Card\Card;
use App\Card\CardGraphic;

class BlackJackServiceTest extends TestCase
{
    /**
     * @var BlackJackService
     */
    private $service;
    private Deck $deck;
    /** @var string[] $playerNames Array containing the names of players. */
    private array $playerNames = [];

    protected function setUp(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturn(new Card('Hearts', '2'));
        $this->playerNames = ['Alice', 'Bob'];
        $this->service = new BlackJackService($this->deck, count($this->playerNames), $this->playerNames);
    }

    public function testPlaceBet(): void
    {
        $this->service->placeBet(0, 10);
        $this->assertEquals([10, 0], $this->service->getBets());
    }

    public function testPlayerDrawCard(): void
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->assertEquals(6, $this->service->getPlayerScore(0));
    }

    public function testBankDrawCard(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', 'Ace')
        );

        $this->service->startGame();
        $this->service->bankDrawCard();
        $this->assertEquals(18, $this->service->getBankScore());
    }

    public function testAreAllPlayersBust(): void
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->assertFalse($this->service->areAllPlayersBust());
    }

    public function testDetermineWinner(): void
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(1);
        $this->service->playerDrawCard(1);
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertIsArray($winners);
    }

    public function testResetGame(): void
    {
        $this->service->startGame();
        $this->service->resetGame();
        $this->assertEquals(0, $this->service->getBankScore());
        $this->assertEquals([0, 0], $this->service->getBets());
        $this->assertFalse($this->service->isGameOver());
        $this->assertFalse($this->service->isStarted());
    }

    public function testGetPlayerCards(): void
    {
        $this->service->startGame();
        $cards = $this->service->getPlayerCards(0);
        $this->assertIsArray($cards);
        $this->assertCount(2, $cards);
    }

    public function testGetBankCards(): void
    {
        $this->service->startGame();
        $cards = $this->service->getBankCards();
        $this->assertIsArray($cards);
        $this->assertCount(1, $cards);
    }

    public function testGetDealerFirstCard(): void
    {
        $this->service->startGame();
        $card = $this->service->getDealerFirstCard();
        $this->assertInstanceOf(CardGraphic::class, $card);
    }

    public function testIsStarted(): void
    {
        $this->assertFalse($this->service->isStarted());
        $this->service->startGame();
        $this->assertTrue($this->service->isStarted());
    }

    public function testPlayerBusts(): void
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->assertFalse($this->service->getPlayers()[0]->isBust());
    }

    public function testCalculateCardValueAce(): void
    {
        $player = new Player('TestPlayer');
        $player->addToScore(10);
        $card = new Card('Spades', 'Ace');

        $this->assertEquals(11, $this->service->calculateCardValue($card, $player));

        $player->addToScore(10);
        $this->assertEquals(1, $this->service->calculateCardValue($card, $player));
    }

    public function testCalculateCardValueFaceCard(): void
    {
        $player = new Player('TestPlayer');
        $card = new Card('Spades', 'King');

        $this->assertEquals(10, $this->service->calculateCardValue($card, $player));
    }

    public function testIsGameOver(): void
    {
        $this->assertFalse($this->service->isGameOver());
    }

    public function testAreAllPlayersBustNoneBust(): void
    {
        $this->service->startGame();
        foreach ($this->playerNames as $playerName) {
            $playerIndex = array_search($playerName, $this->playerNames);
            if (is_int($playerIndex)) {
                while ($this->service->getPlayerScore($playerIndex) < 20) {
                    $this->service->playerDrawCard($playerIndex);
                }
            }
        }
        $this->assertFalse($this->service->areAllPlayersBust());
    }

    public function testAreAllPlayersBustOneOrMoreBust(): void
    {
        $this->service->startGame();
        foreach ($this->playerNames as $playerName) {
            $playerIndex = array_search($playerName, $this->playerNames);
            if (is_int($playerIndex)) {
                while ($this->service->getPlayerScore($playerIndex) < 22) {
                    $this->service->playerDrawCard($playerIndex);
                }
            }
        }
        $this->assertTrue($this->service->areAllPlayersBust());
    }

    public function testBankDrawCardBust(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Hearts', '10'),
            new Card('Hearts', '10'),
            new Card('Hearts', '10'),
            new Card('Hearts', '10'),
            new Card('Hearts', 'Ace')
        );

        $this->service->startGame();
        $this->service->bankDrawCard();
        $this->assertTrue($this->service->isGameOver());
    }

    public function testBreakLoopIfNoCardDrawn(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturn(null);
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->assertFalse($this->service->isGameOver());
    }

    public function testGetDealerFirstCardReturnsNullIfNoCards(): void
    {
        $this->assertNull($this->service->getDealerFirstCard());
    }

    public function testDetermineWinnerAllBust(): void
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }

    public function testDetermineWinnerBankBusts(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Hearts', '9'),
            new Card('Hearts', '10'),
            new Card('Hearts', 'King'),
            new Card('Hearts', '3'),
            new Card('Hearts', '2'),
            new Card('Hearts', 'Ace')
        );

        $this->service->startGame();
        $this->service->placeBet(0, 10);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->bankDrawCard();
        $this->service->bankDrawCard();
        $this->service->bankDrawCard();
        $this->service->bankDrawCard();
        $this->service->bankDrawCard();
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }

    public function testDetermineWinnerDraw(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Diamond', '10'),
            new Card('Hearts', '10'),
            new Card('Diamond', 'King'),
            new Card('Hearts', 'King'),
        );

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Draw', 'Draw'], $winners);
    }

    public function testDetermineWinnerBankWins(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Diamond', '10'),
            new Card('Hearts', '7'),
            new Card('Diamond', 'King'),
            new Card('Hearts', 'Queen'),
        );

        $this->service->startGame();
        $this->service->placeBet(0, 10);
        $this->service->playerDrawCard(0);
        $this->service->bankDrawCard();
        $this->service->playerDrawCard(1);
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }
}
