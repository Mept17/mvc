<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $books = [
            [
                'title' => 'Book One',
                'isbn' => '1234567890123',
                'author' => 'Author One',
                'image' => 'image1.jpg',
            ],
            [
                'title' => 'Book Two',
                'isbn' => '1234567890124',
                'author' => 'Author Two',
                'image' => 'image2.jpg',
            ],
            [
                'title' => 'Book Three',
                'isbn' => '1234567890125',
                'author' => 'Author Three',
                'image' => 'image3.jpg',
            ],
        ];

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setIsbn($bookData['isbn']);
            $book->setAuthor($bookData['author']);
            $book->setImage($bookData['image']);
            $manager->persist($book);
        }

        $manager->flush();
    }
}
