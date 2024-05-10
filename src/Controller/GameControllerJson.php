<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Game\Game;

class GameControllerJson extends AbstractController
{
    #[Route("/api/game", name: "api_game_status")]
    public function apiGameStatus(Request $request): JsonResponse
    {
        $game = $request->getSession()->get('game');

        if (!$game instanceof Game) {
            return new JsonResponse(['error' => 'No active game found'], Response::HTTP_NOT_FOUND);
        }

        $gameStatus = [
            'player_score' => $game->getPlayerScore(),
            'bank_score' => $game->getBankScore(),
            'game_over' => $game->isGameOver(),
            'player_cards' => [],
            'bank_cards' => [],
        ];

        foreach ($game->getPlayerCards() as $playerCard) {
            $gameStatus['player_cards'][] = [
                $playerCard->getSuit(),
                $playerCard->getValue(),
            ];
        }

        foreach ($game->getBankCards() as $bankCard) {
            $gameStatus['bank_cards'][] = [
                $bankCard->getSuit(),
                $bankCard->getValue(),
            ];
        }

        return new JsonResponse($gameStatus);
    }
}
