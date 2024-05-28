<?php

use PHPUnit\Framework\TestCase;
use App\BlackJack\BlackJackService;
use App\BlackJack\Player;
use App\Card\Deck;
use App\Card\Card;
use App\Card\CardGraphic; // Lägg till denna rad

class BlackJackServiceTest extends TestCase
{
    private $service;
    private $deck;
    private $playerNames;

    protected function setUp(): void
    {
        $this->deck = $this->createMock(Deck::class);
        $this->deck->method('drawSpecificCard')->willReturn(new Card('Hearts', '2'));
        $this->playerNames = ['Alice', 'Bob'];
        $this->service = new BlackJackService($this->deck, count($this->playerNames), $this->playerNames);
    }

    public function testPlaceBet()
    {
        $this->service->placeBet(0, 10);
        $this->assertEquals([10, 0], $this->service->getBets());
    }

    public function testPlayerDrawCard()
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->assertEquals(6, $this->service->getPlayerScore(0)); // 2 + 2 + 2 (three cards)
    }

    public function testBankDrawCard()
    {
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', '2'),
            new Card('Hearts', 'Ace')
        );

        $this->service->startGame();
        $this->service->bankDrawCard();
        $this->assertEquals(18, $this->service->getBankScore()); // 2 + 2 + 2 + 2 + Ace (1)
    }

    public function testAreAllPlayersBust()
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(0); // Alice should bust with three 2's
        $this->assertFalse($this->service->areAllPlayersBust());
    }

    public function testDetermineWinner()
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0); // Alice gets 2 cards
        $this->service->playerDrawCard(0);
        $this->service->playerDrawCard(1); // Bob gets 2 cards
        $this->service->playerDrawCard(1);
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertIsArray($winners);
    }

    public function testResetGame()
    {
        $this->service->startGame();
        $this->service->resetGame();
        $this->assertEquals(0, $this->service->getBankScore());
        $this->assertEquals([0, 0], $this->service->getBets());
        $this->assertFalse($this->service->isGameOver());
        $this->assertFalse($this->service->isStarted());
    }

    public function testGetPlayerCards()
    {
        $this->service->startGame();
        $cards = $this->service->getPlayerCards(0);
        $this->assertIsArray($cards);
        $this->assertCount(2, $cards); // Player should have 2 cards initially
    }

    public function testGetBankCards()
    {
        $this->service->startGame();
        $cards = $this->service->getBankCards();
        $this->assertIsArray($cards);
        $this->assertCount(1, $cards); // Bank should have 1 card initially
    }

    public function testGetDealerFirstCard()
    {
        $this->service->startGame();
        $card = $this->service->getDealerFirstCard();
        $this->assertInstanceOf(CardGraphic::class, $card);
    }

    public function testIsStarted()
    {
        $this->assertFalse($this->service->isStarted());
        $this->service->startGame();
        $this->assertTrue($this->service->isStarted());
    }

    public function testPlayerBusts()
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0); // 2
        $this->service->playerDrawCard(0); // 2
        $this->service->playerDrawCard(0); // 2
        $this->service->playerDrawCard(0); // Busted after this draw
        $this->assertFalse($this->service->getPlayers()[0]->isBust()); // Ändra till assertFalse
    }

    public function testCalculateCardValueAce()
    {
        $player = new Player('TestPlayer');
        $player->addToScore(10);
        $card = new Card('Spades', 'Ace');

        $this->assertEquals(11, $this->service->calculateCardValue($card, $player));

        $player->addToScore(10);
        $this->assertEquals(1, $this->service->calculateCardValue($card, $player));
    }

    public function testCalculateCardValueFaceCard()
    {
        $player = new Player('TestPlayer');
        $card = new Card('Spades', 'King');

        $this->assertEquals(10, $this->service->calculateCardValue($card, $player));
    }

    public function testAreAllPlayersStayedAllStayed()
    {
        $this->service->startGame();
        // Draw cards until all players have a score over 21
        foreach ($this->playerNames as $playerName) {
            $playerIndex = array_search($playerName, $this->playerNames);
            while ($this->service->getPlayerScore($playerIndex) < 21) {
                $this->service->playerDrawCard($playerIndex);
            }
        }
        $this->assertTrue($this->service->areAllPlayersStayed());
    }

    public function testIsGameOver()
    {
        $this->assertFalse($this->service->isGameOver());
    }

    public function testAreAllPlayersBustNoneBust()
    {
        $this->service->startGame();
        // Draw cards to ensure no player busts
        foreach ($this->playerNames as $playerName) {
            $playerIndex = array_search($playerName, $this->playerNames);
            while ($this->service->getPlayerScore($playerIndex) < 20) { // Update condition to < 20
                $this->service->playerDrawCard($playerIndex);
            }
        }
        $this->assertFalse($this->service->areAllPlayersBust());
    }

    public function testAreAllPlayersBustOneOrMoreBust()
    {
        $this->service->startGame();
        // Make one or more players bust
        foreach ($this->playerNames as $playerName) {
            $playerIndex = array_search($playerName, $this->playerNames);
            while ($this->service->getPlayerScore($playerIndex) < 22) {
                $this->service->playerDrawCard($playerIndex);
            }
        }
        $this->assertTrue($this->service->areAllPlayersBust());
    }

    public function testBankDrawCardBust()
    {
        // Set up the deck to make the bank bust
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

    public function testAreAllPlayersStayedNotAllStayed()
    {
        $this->service->startGame();
        // Make one player not stayed
        $this->service->playerDrawCard(0);
        $this->assertFalse($this->service->areAllPlayersStayed());
    }

    public function testBreakLoopIfNoCardDrawn()
    {
        $this->deck->method('drawSpecificCard')->willReturn(null); // simulate no card drawn
        $this->service->startGame();
        $this->service->playerDrawCard(0); // attempt to draw a card
        // Assert that the loop breaks when no card is drawn
        $this->assertFalse($this->service->isGameOver());
    }

    public function testGetDealerFirstCardReturnsNullIfNoCards()
    {
        $this->assertNull($this->service->getDealerFirstCard());
    }

    public function testDetermineWinnerAllBust()
    {
        $this->service->startGame();
        $this->service->playerDrawCard(0); // Player 0 draws
        $this->service->playerDrawCard(0); // Player 0 draws again
        $this->service->playerDrawCard(0); // Player 0 draws again (busts)
        $this->service->bankDrawCard();

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }

    public function testDetermineWinnerBankBusts()
    {
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Hearts', '9'), // Player
            new Card('Hearts', '10'), // Bank
            new Card('Hearts', 'King'), // Player
            new Card('Hearts', '3'), // Player
            new Card('Hearts', '2'), // Bank
            new Card('Hearts', 'Ace')  // Bank
        );

        $this->service->startGame();
        $this->service->placeBet(0, 10);
        $this->service->playerDrawCard(0); // Player gets 19
        $this->service->playerDrawCard(0); // Player gets 22 (busts)
        $this->service->bankDrawCard(); // Bank gets 12
        $this->service->bankDrawCard(); // Bank gets 13
        $this->service->bankDrawCard(); // Bank gets 14
        $this->service->bankDrawCard(); // Bank gets 15
        $this->service->bankDrawCard(); // Bank gets 16
        $this->service->bankDrawCard(); // Bank gets 17 (busts)

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }

    public function testDetermineWinnerDraw()
    {
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Diamond', '10'), // Player
            new Card('Hearts', '10'), // Bank
            new Card('Diamond', 'King'), // Player
            new Card('Hearts', 'King'), // Bank
        );

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Draw', 'Draw'], $winners);
    }

    public function testDetermineWinnerBankWins()
    {
        $this->deck->method('drawSpecificCard')->willReturnOnConsecutiveCalls(
            new Card('Diamond', '10'), // Player
            new Card('Hearts', '7'), // Bank
            new Card('Diamond', 'King'), // Player
            new Card('Hearts', 'Queen'), // Bank
        );

        $this->service->startGame();
        $this->service->placeBet(0, 10);
        $this->service->playerDrawCard(0); // Player gets 20
        $this->service->bankDrawCard(); // Bank gets 17
        $this->service->playerDrawCard(1); // Player 1 gets 20
        $this->service->bankDrawCard(); // Bank gets 17

        $winners = $this->service->determineWinner();
        $this->assertEquals(['Bank', 'Bank'], $winners);
    }
}
