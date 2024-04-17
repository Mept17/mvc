<?php

namespace App\Card;

class CardGraphic extends Card
{
    private string $graphic;

    public function __construct(string $suit, string $value, string $graphic)
    {
        parent::__construct($suit, $value);
        $this->graphic = $graphic;
    }

    public function getGraphic(): string
    {
        return $this->graphic;
    }
}