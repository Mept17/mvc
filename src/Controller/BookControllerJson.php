<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookControllerJson extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/library/books", name: "api_library_books")]
    public function apiLibraryBooks(): JsonResponse
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();

        $formattedBooks = [];
        foreach ($books as $book) {
            $formattedBooks[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'isbn' => $book->getIsbn(),
                'author' => $book->getAuthor(),
                'image' => $book->getImage(), // Justera detta till rätt väg om nödvändigt
            ];
        }

        $jsonContent = json_encode($formattedBooks, JSON_PRETTY_PRINT);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route("/api/library/book/{isbn}", name: "api_library_book")]
    public function apiLibraryBook($isbn): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['isbn' => $isbn]);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        $formattedBook = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
        ];

        $jsonContent = json_encode($formattedBook, JSON_PRETTY_PRINT);

        return new JsonResponse($jsonContent, 200, [], true);
    }
}