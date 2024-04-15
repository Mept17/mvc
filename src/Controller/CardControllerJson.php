<?php

namespace App\Controller;

use App\Card\Deck;
use App\Card\Card;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CardControllerJson extends AbstractController
{
    #[Route("/api/deck", name: "api_deck_get", methods: ['GET'])]
    public function getDeck(): JsonResponse
    {
        $deck = Deck::createDeck();

        $deck->sortDeck();

        $formattedDeck = [];
        foreach ($deck->getAllCards() as $card) {
            $formattedDeck[] = $card->getSuit() . ' ' . $card->getValue();
        }

        return $this->json($formattedDeck);
    }

    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ['GET', 'POST'])]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', Deck::createDeck());

        $deck->shuffle();

        $session->set('deck', $deck);

        $formattedDeck = [];
        foreach ($deck->getAllCards() as $card) {
            $formattedDeck[] = $card->getSuit() . ' ' . $card->getValue();
        }

        return $this->json($formattedDeck);
    }

    #[Route("/api/deck/draw", name: "api_deck_draw", methods: ['GET', 'POST'])]
    public function drawCard(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', Deck::createDeck());

        $drawnCard = $deck->drawSpecificCard(0);

        $session->set('deck', $deck);

        $formattedDrawnCard = $drawnCard ? $drawnCard->getSuit() . ' ' . $drawnCard->getValue() : null;

        return $this->json([
            'drawn_card' => $formattedDrawnCard,
            'remaining_cards' => count($deck->getAllCards()),
        ]);
    }

    #[Route("/api/deck/draw/{number}", name: "api_deck_draw_multiple", methods: ['GET', 'POST'])]
    public function drawMultipleCards(SessionInterface $session, $number): JsonResponse
    {
        $deck = $session->get('deck', Deck::createDeck());

        $drawnCards = [];
        for ($i = 0; $i < $number; $i++) {
            $drawnCards[] = $deck->drawSpecificCard(0);
        }

        $session->set('deck', $deck);

        $formattedDrawnCards = [];
        foreach ($drawnCards as $card) {
            $formattedDrawnCards[] = $card ? $card->getSuit() . ' ' . $card->getValue() : null;
        }

        return $this->json([
            'drawn_cards' => $formattedDrawnCards,
            'remaining_cards' => count($deck->getAllCards()),
        ]);
    }
}
