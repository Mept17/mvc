<?php

namespace App\Controller;

use App\Card\Deck;
use App\Game\Game;
use App\Card\CardGraphic;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameController extends AbstractController
{
    #[Route("/game", name: "landing_page")]
    public function landingPage(): Response
    {
        return $this->render('game/landing_page.html.twig');
    }

    #[Route("/game/doc", name: "game_documentation")]
    public function gameDocumentation(): Response
    {
        return $this->render('game/documentation.html.twig');
    }

    #[Route("/game/card", name: "21_page")]
    public function index(Request $request): Response
    {
        $game = $request->getSession()->get('game');

        if (!$game instanceof Game) {
            return $this->render('error.html.twig', ['message' => 'No active game found']);
        }

        $playerCards = $game->getPlayerCards();
        $bankCards = $game->getBankCards();

        $drawnCardValue = null;
        if ($request->getSession()->has('drawn_card_value')) {
            $drawnCardValue = $request->getSession()->get('drawn_card_value');
        }

        $gameOver = $game->isGameOver() || $game->getPlayerScore() > 21;

        $winner = '';
        if ($gameOver) {
            $winner = $game->determineWinner();
        }

        return $this->render('game/card_page.html.twig', [
            'playerScore' => $game->getPlayerScore(),
            'bankScore' => $game->getBankScore(),
            'gameOver' => $gameOver,
            'playerCards' => $playerCards,
            'bankCards' => $bankCards,
            'drawnCardValue' => $drawnCardValue,
            'winner' => $winner,
            'playerDrawn' => $game->isPlayerDrawn(),
            'game' => $game,
        ]);
    }

    #[Route("/game/card/start", name: "start_game")]
    public function startGame(Request $request): Response
    {
        $deck = Deck::createDeck('Card');
        $deck->shuffle();
        $game = new Game($deck);
        $request->getSession()->set('game', $game);

        return $this->redirectToRoute('21_page');
    }

    #[Route("/game/card/player-draw", name: "player_draw")]
    public function playerDraw(Request $request): Response
    {
        $game = $request->getSession()->get('game');
        if (!$game instanceof Game) {
            $deck = Deck::createDeck('Card');
            $deck->shuffle();
            $game = new Game($deck);
            $request->getSession()->set('game', $game);
        }

        $game->playerDrawCard();
        $request->getSession()->set('game', $game);

        return $this->redirectToRoute('21_page');
    }

    #[Route("/game/card/bank-draw", name: "bank_draw")]
    public function bankDraw(Request $request): Response
    {
        $game = $request->getSession()->get('game');
        if ($game instanceof Game) {
            $game->bankDrawCard();
            $request->getSession()->set('game', $game);
        }

        return $this->redirectToRoute('21_page');
    }

    #[Route("/game/card/reset", name: "reset_game")]
    public function resetGame(Request $request): Response
    {
        $deck = Deck::createDeck('Card');
        $deck->shuffle();
        $game = new Game($deck);
        $request->getSession()->set('game', $game);

        return $this->redirectToRoute('21_page');
    }
}
