<?php

namespace App\BlackJack;

use App\Card\Deck;
use App\BlackJack\Player;
use App\Card\CardGraphic;

/**
 * Hanterar spelet Blackjack inklusive hantering av kortlekar, spelare, satsningar och spelets logik.
 */
class BlackJackService
{
    private Deck $deck;
    private array $players;
    private Player $bank;
    private bool $gameOver;
    private array $bets;
    private bool $started;
    private bool $resultsCalculated;

    /**
     * Konstruktor för att initialisera en ny BlackJackService-instans.
     *
     * @param Deck $deck En kortlek för spelet.
     * @param int $numPlayers Antalet spelare.
     * @param array $playerNames En array med namnen på spelarna.
     */
    public function __construct(Deck $deck, int $numPlayers, array $playerNames)
    {
        $this->deck = $deck;
        $this->players = [];
        for ($i = 0; $i < $numPlayers; $i++) {
            $this->players[] = new Player($playerNames[$i], 100); // Initial amount of money
        }
        $this->bank = new Player('Bank');
        $this->gameOver = false;
        $this->bets = array_fill(0, $numPlayers, 0);
        $this->started = false;
        $this->resultsCalculated = false;
    }

    /**
     * Placera en satsning för en spelare.
     *
     * @param int $playerIndex Index för spelaren.
     * @param int $amount Beloppet som ska satsas.
     * @return void
     */
    public function placeBet(int $playerIndex, int $amount): void
    {
        $this->bets[$playerIndex] = $amount;
        $this->players[$playerIndex]->adjustMoney(-$amount); // Subtrahera bet beloppet direkt
    }

    /**
     * Hämta satsningarna för alla spelare.
     *
     * @return array Satsningarna för alla spelare.
     */
    public function getBets(): array
    {
        return $this->bets;
    }

    /**
     * Hämta poängen för en specifik spelare.
     *
     * @param int $playerIndex Index för spelaren.
     * @return int Poängen för spelaren.
     */
    public function getPlayerScore(int $playerIndex): int
    {
        return $this->players[$playerIndex]->getScore();
    }

    /**
     * Hämta poängen för banken.
     *
     * @return int Poängen för banken.
     */
    public function getBankScore(): int
    {
        return $this->bank->getScore();
    }

    /**
     * Kontrollera om spelet är över.
     *
     * @return bool True om spelet är över, annars false.
     */
    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    /**
     * Låt en spelare dra ett kort.
     *
     * @param int $playerIndex Index för spelaren som drar kortet.
     * @return void
     */
    public function playerDrawCard(int $playerIndex): void
    {
        if (!$this->gameOver) {
            $card = $this->deck->drawSpecificCard(0);
            if ($card !== null) {
                $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                $value = $this->calculateCardValue($card, $this->players[$playerIndex]);
                $this->players[$playerIndex]->addToScore($value);
                $this->players[$playerIndex]->addCard($graphicCard);
                if ($this->players[$playerIndex]->getScore() > 21) {
                    // Mark player as bust
                    $this->players[$playerIndex]->setBust(true);
                }
            }
        }
    }

    /**
     * Kontrollera om alla spelare är "busted".
     *
     * @return bool True om alla spelare är "busted", annars false.
     */
    public function areAllPlayersBust(): bool
    {
        foreach ($this->players as $player) {
            if (!$player->isBust()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Banken drar ett kort.
     *
     * @return void
     */
    public function bankDrawCard(): void
    {
        if (!$this->gameOver) {
            while ($this->bank->getScore() < 17) {
                $card = $this->deck->drawSpecificCard(0);
                if ($card !== null) {
                    $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                    $value = $this->calculateCardValue($card, $this->bank);
                    $this->bank->addToScore($value);
                    $this->bank->addCard($graphicCard);
                // } else {
                //     break;
                }
            }
            $this->gameOver = true;
        }
    }

    /**
     * Värdet på vissa kort.
     *
     * @param \App\Card\Card $card Det kort vars värde ska beräknas.
     * @param Player $player Spelaren som drar kortet.
     * @return int Värdet på kortet.
     */
    public function calculateCardValue(\App\Card\Card $card, Player $player): int
    {
        $value = $card->getValue();
        if ($value === 'Ace') {
            $currentScore = $player->getScore();
            return ($currentScore + 11 > 21) ? 1 : 11;
        } elseif (in_array($value, ['King', 'Queen', 'Jack'])) {
            return 10;
        }
        return (int)$value;
    }

    /**
     * Återställ spelet till sitt ursprungliga tillstånd för en ny omgång.
     *
     * @return void
     */
    public function resetGame(): void
    {
        foreach ($this->players as $player) {
            $player->resetScore();
        }
        $this->bank->resetScore();
    
        $this->deck = Deck::createDeck('Card');
        $this->deck->shuffle();
    
        $this->gameOver = false;
        $this->bets = array_fill(0, count($this->players), 0);
        $this->started = false;
        $this->resultsCalculated = false;
    }

    /**
     * Kollar vem som vinner.
     *
     * @return array En array med vinnarna av spelet.
     */
    public function determineWinner(): array
    {
        if ($this->resultsCalculated) {
            return [];
        }
    
        $winners = [];
        $bankScore = $this->bank->getScore();
        $players = $this->getPlayers();
    
        foreach ($players as $index => $player) {
            $playerScore = $player->getScore();
            if ($playerScore > 21) {
                $winners[$index] = 'Bank';
            } elseif ($bankScore > 21 || $playerScore > $bankScore) {
                $winners[$index] = 'Player';
                $player->adjustMoney($this->bets[$index] * 2); // Dubbel pengarna vid vinst
            } elseif ($playerScore < $bankScore) {
                $winners[$index] = 'Bank';
            } else {
                $winners[$index] = 'Draw';
                $player->adjustMoney($this->bets[$index]); // Returnera bet pengarna vid oavgjort
            }
        }
    
        $this->resultsCalculated = true;
        return $winners;
    }
    

    /**
     * Hämta spelarens kort.
     *
     * @param int $playerIndex Index för spelaren.
     * @return array Spelarens kort.
     */
    public function getPlayerCards(int $playerIndex): array
    {
        return $this->players[$playerIndex]->getCards();
    }

    /**
     * Hämta bankens kort.
     *
     * @return array Bankens kort.
     */
    public function getBankCards(): array
    {
        return $this->bank->getCards();
    }

    /**
     * Hämtar alla spelare i spelet.
     *
     * @return array Alla spelare i spelet.
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Kontrollera om spelet har startat.
     *
     * @return bool True om spelet har startat, annars false.
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Starta spelet genom att dela ut kort till spelarna som deltar och banken.
     *
     * @return void
     */
    public function startGame(): void
    {
        if (!$this->started) {
            // Deal one card to the dealer (first card)
            $this->dealerDrawCard();
            
            // Deal two cards to each player
            foreach ($this->players as $playerIndex => $player) {
                $this->playerDrawCard($playerIndex);
                $this->playerDrawCard($playerIndex);
            }
            
            $this->started = true;
        }
    }

    /**
     * Låt banken dra sitt första kort som är synligt för spelarna.
     *
     * @return void
     */
    public function dealerDrawCard(): void
    {
        if (!$this->gameOver) {
            $card = $this->deck->drawSpecificCard(0);
            if ($card !== null) {
                $graphicCard = new CardGraphic($card->getSuit(), $card->getValue());
                $value = $this->calculateCardValue($card, $this->bank); // Uppdaterat anrop med $this->bank
                $this->bank->addCard($graphicCard);
                $this->bank->addToScore($value);
            }
        }
    }

    /**
     * Hämta det första kortet som banken har.
     *
     * @return CardGraphic|null Det första kortet som banken har.
     */
    public function getDealerFirstCard(): ?CardGraphic
    {
        $dealerCards = $this->bank->getCards();
        if (!empty($dealerCards)) {
            return $dealerCards[0];
        }
        return null;
    }
}
