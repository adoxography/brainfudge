<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Brainfudge\Brainfudge;

final class BrainfudgeTest extends TestCase
{
    public function testItReturnsAString(): void
    {
        $this->assertEquals('', Brainfudge::run(''));
    }

    public function testItPrintsAnAByBruteForce(): void
    {
        $input = str_repeat('+', 65) . '.';
        $output = Brainfudge::run($input);
        $this->assertEquals('A', $output);
    }

    public function testItPrintsAnAIntelligently(): void
    {
        $input = '----[---->+<]>++.';
        $output = Brainfudge::run($input);
        $this->assertEquals('A', $output);
    }

    public function testItRaisesAnErrorWhenGoneTooFarLeft(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        Brainfudge::run('<');
    }
}
