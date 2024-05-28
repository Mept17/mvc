<?php

namespace App\Controller;

use App\Card\Deck;
use App\BlackJack\BlackJackService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller för att hantera BlackJack-spelet.
 */
class BlackJackController extends AbstractController
{
    /**
     * Renderar landningssidan för BlackJack-spelet.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("/proj", name: "blackjack_landing")]
    public function landingPage(Request $request): Response
    {
        $request->getSession()->clear();
        return $this->render('proj/landing.html.twig');
    }

    /**
     * Renderar sidan "About" för BlackJack-spelet.
     *
     * @return Response HTTP-svar.
     */
    #[Route("/proj/about", name: "blackjack_about")]
    public function aboutPage(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    /**
     * Renderar själva BlackJack-spelet.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("/proj/blackjack", name: "blackjack_game")]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $game = $session->get('game');

        if (!$game instanceof BlackJackService) {
            return $this->redirectToRoute('blackjack_start');
        }

        $players = $game->getPlayers();

        if ($game->isGameOver() && $session->get('round_over')) {
            $playerCards = [];
            foreach ($players as $index => $player) {
                $playerCards[] = $game->getPlayerCards($index);
            }
            $bankCards = $game->getBankCards();
            $dealerFirstCard = $game->getDealerFirstCard();

            $winners = $game->determineWinner();

            return $this->render('proj/blackjack.html.twig', [
                'players' => $players,
                'bankScore' => $game->getBankScore(),
                'playerCards' => $playerCards,
                'bankCards' => $bankCards,
                'dealerFirstCard' => $dealerFirstCard,
                'gameOver' => true,
                'winners' => $winners,
                'gameStarted' => $game->isStarted(),
                'playersWithBets' => $game->getBets()
            ]);
        }

        $playersWithBets = [];
        foreach ($players as $index => $player) {
            if ($game->getBets()[$index] > 0) {
                $playersWithBets[] = $index;
            }
        }

        $allPlayersBroke = true;
        foreach ($players as $player) {
            if ($player->getMoney() > 0) {
                $allPlayersBroke = false;
                break;
            }
        }

        if ($allPlayersBroke && !$game->isGameOver() && empty($playersWithBets)) {
            return $this->render('proj/broke.html.twig');
        }

        $playerCards = [];
        foreach ($players as $index => $player) {
            $playerCards[] = $game->getPlayerCards($index);
        }
        $bankCards = $game->getBankCards();
        $dealerFirstCard = $game->getDealerFirstCard();

        $gameOver = $game->isGameOver();
        $winners = [];
        if ($gameOver) {
            $winners = $game->determineWinner();
            $session->set('round_over', true);
        }

        return $this->render('proj/blackjack.html.twig', [
            'players' => $players,
            'bankScore' => $game->getBankScore(),
            'playerCards' => $playerCards,
            'bankCards' => $bankCards,
            'dealerFirstCard' => $dealerFirstCard,
            'gameOver' => $gameOver,
            'winners' => $winners,
            'gameStarted' => $game->isStarted(),
            'playersWithBets' => $playersWithBets,
        ]);
    }

    /**
     * Startar ett nytt BlackJack-spel.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("/proj/blackjack/start", name: "blackjack_start", methods: ["POST"])]
    public function startGame(Request $request): Response
    {
        $game = $request->getSession()->get('game');

        if ($game instanceof BlackJackService) {
            return $this->redirectToRoute('blackjack_game');
        }

        $numPlayers = $request->request->getInt('num_players', 1);
        $playerNames = [];
        for ($i = 1; $i <= $numPlayers; $i++) {
            $playerNames[] = $request->request->get('player_name_'.$i, 'Player '.$i);
        }
        $deck = Deck::createDeck('Card');
        $deck->shuffle();
        $playerNames = array_map('strval', $playerNames);
        $game = new BlackJackService($deck, $numPlayers, $playerNames);
        $request->getSession()->set('game', $game);
        $request->getSession()->set('player_names', $playerNames);
        $request->getSession()->set('num_players', $numPlayers);

        return $this->redirectToRoute('blackjack_game');
    }

    /**
     * Hanterar när en spelare drar ett kort.
     *
     * @param Request $request HTTP-förfrågan.
     * @param int $playerIndex Index för spelaren som drar kort.
     * @return Response HTTP-svar.
     */
    #[Route("/proj/blackjack/player/{playerIndex}", name: "blackjack_player_draw", requirements: ["playerIndex" => "\d+"])]
    public function playerDraw(Request $request, int $playerIndex): Response
    {
        $game = $request->getSession()->get('game');
        if ($game instanceof BlackJackService) {
            $game->playerDrawCard($playerIndex);

            if ($game->areAllPlayersBust()) {
                $game->bankDrawCard();
            }

            $request->getSession()->set('game', $game);
        }

        return $this->redirectToRoute('blackjack_game');
    }

    /**
     * Hanterar när banken (dealern) drar kort.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("/proj/blackjack/bank", name: "blackjack_bank_draw")]
    public function bankDraw(Request $request): Response
    {
        $game = $request->getSession()->get('game');
        if ($game instanceof BlackJackService) {
            $game->bankDrawCard();
            $request->getSession()->set('game', $game);
            $request->getSession()->set('bank_drawn', true);
        }

        return $this->redirectToRoute('blackjack_game');
    }

    /**
     * Återställer BlackJack-spelet till sitt ursprungliga tillstånd.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("proj/blackjack/reset", name: "blackjack_reset")]
    public function resetGame(Request $request): Response
    {
        $game = $request->getSession()->get('game');
        if ($game instanceof BlackJackService) {
            $game->resetGame();
            $request->getSession()->set('game', $game);
        }

        return $this->redirectToRoute('blackjack_game');
    }

    /**
     * Hanterar när spelare placerar sina insatser.
     *
     * @param Request $request HTTP-förfrågan.
     * @return Response HTTP-svar.
     */
    #[Route("/proj/blackjack/bets", name: "blackjack_place_bets", methods: ["POST"])]
    public function placeBets(Request $request): Response
    {
        $game = $request->getSession()->get('game');

        if ($game instanceof BlackJackService) {
            foreach ($game->getPlayers() as $index => $player) {
                if ($player->getMoney() <= 0) {
                    continue;
                }
                $bet = $request->request->getInt('bet_'.$index, 0);
                $game->placeBet($index, $bet);
            }
            $game->startGame();
            $request->getSession()->set('game', $game);
        }

        return $this->redirectToRoute('blackjack_game');
    }
}
