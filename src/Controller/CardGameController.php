<?php

namespace App\Controller;

use App\Card\Deck;
use App\Card\Card;
use App\Card\CardGraphic;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card_page")]
    public function landingPage(): Response
    {
        return $this->render('card/card_page.html.twig');
    }

    #[Route("/session", name: "show_session")]
    public function showSession(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        dump($deck);

        $sessionData = [];

        if ($deck instanceof Deck) {
            $sessionData['deck'] = $deck;
        }

        return $this->render('card/show_session.html.twig', [
            'sessionData' => $sessionData
        ]);
    }

    #[Route("/session/delete", name: "delete_session")]
    public function deleteSession(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('notice', 'Session cleared successfully.');
        return $this->redirectToRoute('card_page');
    }

    #[Route("/card/deck", name: "show_deck")]
    public function showDeck(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck instanceof Deck || !$deck->isSorted()) {
            $deck = Deck::createDeck('CardGraphic');
            $deck->sortDeck();
            $session->set('deck', $deck);
        }

        return $this->render('card/show_deck.html.twig', [
            'sorted_deck' => $deck->getAllCards()
        ]);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuffleDeck(SessionInterface $session): Response
    {
        $deck = Deck::createDeck('CardGraphic');
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->redirectToRoute('show_shuffled_deck');
    }

    #[Route("/card/deck/shuffled", name: "show_shuffled_deck")]
    public function showShuffledDeck(SessionInterface $session): Response
    {
        $deck = $session->get('deck');
        return $this->render('card/show_shuffled_deck.html.twig', [
            'shuffled_deck' => $deck->getAllCards()
        ]);
    }

    #[Route("/card/deck/draw", name: "draw_card")]
    public function drawCard(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck instanceof Deck) {
            $deck = Deck::createDeck('CardGraphic');
            $deck->sortDeck();
            $session->set('deck', $deck);
        }

        $remainingCardsCount = count($deck->getAllCards());
        if ($remainingCardsCount == 0) {
            return $this->render('card/no_cards_left.html.twig', [
                'remaining_cards' => $remainingCardsCount
            ]);
        }

        $drawnIndex = array_rand($deck->getAllCards(), 1);
        $drawnCard = $deck->drawSpecificCard($drawnIndex);

        $session->set('deck', $deck);

        return $this->render('card/draw_card.html.twig', [
            'drawn_card' => $drawnCard,
            'remaining_cards' => count($deck->getAllCards())
        ]);
    }

    #[Route("/card/deck/draw/{number}", name: "draw_cards")]
    public function drawCards(int $number, SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck instanceof Deck) {
            $deck = Deck::createDeck('CardGraphic');
            $deck->sortDeck();
            $session->set('deck', $deck);
        }

        $remainingCardsCount = count($deck->getAllCards());

        if ($number > $remainingCardsCount) {
            return $this->render('card/not_enough_cards.html.twig', [
                'remaining_cards' => $remainingCardsCount,
                'number' => $number
            ]);
        }

        $drawnCards = [];
        for ($i = 0; $i < $number; $i++) {
            $drawnIndex = array_rand($deck->getAllCards(), 1);
            $drawnCard = $deck->drawSpecificCard($drawnIndex);
            if ($drawnCard) {
                $drawnCards[] = $drawnCard;
            }
        }

        $session->set('deck', $deck);

        return $this->render('card/draw_cards.html.twig', [
            'drawn_cards' => $drawnCards,
            'remaining_cards' => count($deck->getAllCards())
        ]);
    }
}
