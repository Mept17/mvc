<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteControllerJson extends AbstractController
{
    #[Route("/api/", name: "api_landing")]
    public function apiLanding(): Response
    {
        $routes = [
            'quote' => $this->generateUrl('api_quote'), // generate URL for the 'quote' route
            'lucky_number' => $this->generateUrl('api_lucky_number'), // generate URL for the 'lucky_number' route
            'lucky_hi' => $this->generateUrl('api_lucky_hi'), // generate URL for the 'lucky_hi' route
        ];

        // Convert the routes array to JSON format
        $data = [
            'routes' => $routes
        ];

        // Return a JsonResponse
        return $this->render('api_landing.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route("/api/quote", name: "api_quote")]
    public function apiQuote(): JsonResponse
    {
        // Array of available quotes
        $quotes = [
            'quote1' => "Im not lazy, Im just in energy-saving mode.",
            'quote2' => "Im not clumsy, its just the floor hates me and the walls get in my way.",
            'quote3' => "I intend to live forever. So far, so good." ,
        ];

        // Randomly select a quote
        $randomQuoteKey = array_rand($quotes);
        $randomQuote = $quotes[$randomQuoteKey];

        // Current date and timestamp
        $currentDate = date('Y-m-d');
        $currentTime = time();

        // Build the JSON response
        $data = [
            'quote' => $randomQuote,
            'date' => $currentDate,
            'timestamp' => $currentTime,
        ];

        // Return JsonResponse
        return new JsonResponse($data);
    }
}