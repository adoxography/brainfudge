<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brainfudge\Brainfudge;
use Tests\Util\MockReader;

final class BrainfudgeTest extends TestCase
{
    public function testItStoresAProgram(): void
    {
        $bf = new Brainfudge('foo');
        $this->assertEquals(['f', 'o', 'o'], $bf->program);
    }

    public function testItHasARegistersArray(): void
    {
        $bf = new Brainfudge('');
        $this->assertIsArray($bf->registers);
    }

    public function testItProcessesAPlus(): void
    {
        $bf = new Brainfudge('+');
        $bf->process();
        $this->assertEquals(1, $bf->registers[0]);
    }

    public function testItProcessesPluses(): void
    {
        $bf = new Brainfudge('+++');
        $bf->process();
        $this->assertEquals(3, $bf->registers[0]);
    }

    public function testItProcessesMinuses(): void
    {
        $bf = new Brainfudge('--');
        $bf->process();
        $this->assertEquals(254, $bf->registers[0]);
    }

    public function testValuesWrapOver255(): void
    {
        $input = str_repeat('+', 257);
        $bf = new Brainfudge($input);
        $bf->process();
        $this->assertEquals(1, $bf->registers[0]);
    }

    public function testItProcessesRightAngleBrackets(): void
    {
        $bf = new Brainfudge('>+');
        $bf->process();
        $this->assertEquals(1, $bf->registers[1]);
    }

    public function testItProcessesLeftAngleBrackets(): void
    {
        $bf = new Brainfudge('>><-');
        $bf->process();
        $this->assertEquals(255, $bf->registers[1]);
    }

    public function testItRaisesAnErrorWhenGoneTooFarLeft(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        $bf = new Brainfudge('<');
        $bf->process();
    }

    public function testItReturnsAString(): void
    {
        $bf = new Brainfudge('');
        $this->assertEquals('', $bf->process());
    }

    public function testItProcessesPeriods(): void
    {
        $bf = new Brainfudge('.');
        $this->assertEquals("\x00", $bf->process());
    }

    public function testItProcessesCommas(): void
    {
        $bf = new Brainfudge(',', new MockReader('a'));
        $bf->process();
        $this->assertEquals(97, $bf->registers[0]);
    }

    public function testItProcessesIfs(): void
    {
        $bf = new Brainfudge('[+]-');
        $bf->process();
        $this->assertEquals(255, $bf->registers[0]);
    }

    public function testItProcessesWhiles(): void
    {
        $bf = new Brainfudge('++[>+<-]');
        $bf->process();
        $this->assertEquals(2, $bf->registers[1]);
    }

    public function testItProcessesNestedLoops(): void
    {
        $bf = new Brainfudge('+++[>>[+]<<>+<-]');
        $bf->process();
        $this->assertEquals(3, $bf->registers[1]);
    }

    public function testItHandlesHighNumbers(): void
    {
        $bf = new Brainfudge('++++++++[>++++++++++++++++<-]');
        $bf->process();
        $this->assertEquals(128, $bf->registers[1]);
    }

    public function testItRaisesAnExceptionWhenNoRightBracketToGoTo(): void
    {
        $this->expectException(\LogicException::class);

        $bf = new Brainfudge('[++');
        $bf->process();
    }

    public function testItRaisesAnExceptionWhenNoRightBracketExists(): void
    {
        $this->expectException(\LogicException::class);

        $bf = new Brainfudge('+[++');
        $bf->process();
    }

    public function testItRaisesAnExceptionWhenNoLeftBracketToGoTo(): void
    {
        $this->expectException(\LogicException::class);

        $bf = new Brainfudge('++]');
        $bf->process();
    }

    public function testItRaisesAnExceptionWhenNoLeftBracketExists(): void
    {
        $this->expectException(\LogicException::class);

        $bf = new Brainfudge('>>]');
        $bf->process();
    }

    public function testItRaisesAnExceptionOnUnmatchedBrackets(): void
    {
        $this->expectException(\LogicException::class);

        $bf = new Brainfudge('[[]');
        $bf->process();
    }
}
