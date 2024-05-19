<?php

use PHPUnit\Framework\TestCase;
use App\Card\CardGraphic;

class CardGraphicTest extends TestCase
{
    public function testGetGraphic(): void
    {
        $card = new CardGraphic('Hearts', 'Ace');
        $this->assertEquals('🂱', $card->getGraphic());

        $card = new CardGraphic('Diamonds', 'Jack');
        $this->assertEquals('🃋', $card->getGraphic());
    }

    public function testGetSuitColor(): void
    {
        $card = new CardGraphic('Hearts', 'Ace');
        $this->assertEquals('red', $card->getSuitColor());

        $card = new CardGraphic('Clubs', '2');
        $this->assertEquals('black', $card->getSuitColor());
    }
}
