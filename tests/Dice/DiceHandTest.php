<?php

namespace App\Dice;

use PHPUnit\Framework\TestCase;

// /**
//  * Test cases for class DiceHand.
//  */
// class DiceHandTest extends TestCase
// {
//     /**
//      * Stub the dices to assure the value can be asserted.
//      */
//     public function testAddStubbedDices()
//     {
//         // Create a stub for the Dice class.
//         $stub = $this->createMock(Dice::class);

//         // Configure the stub.
//         $stub->method('roll')
//             ->willReturn(6);
//         // $stub->method('lastRoll')
//         //     ->willReturn(6);

//         $dicehand = new DiceHand();
//         $dicehand->add(clone $stub);
//         $dicehand->add(clone $stub);
//         $dicehand->roll();
//         $res = $dicehand->sum();
//         $this->assertEquals(12, $res);
//     }
// }
