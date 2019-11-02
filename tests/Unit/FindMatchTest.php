<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FindMatchTest extends TestCase
{
    public function testItReturnsFailureWhenNoMatchingRight(): void
    {
        $string = [' [   '];
        $res = findMatch($string, '[', ']', 1, true);
        $this->assertEquals(-1, $res);
    }

    public function testItReturnsFailureWhenNoMatchingLeft(): void
    {
        $string = ['   ]   '];
        $res = findMatch($string, '[', ']', 3, false);
        $this->assertEquals(-1, $res);
    }

    public function testItHandlesOpenOnRightEdge(): void
    {
        $string = ['['];
        $res = findMatch($string, '[', ']', 0, true);
        $this->assertEquals(-1, $res);
    }

    public function testItHandlesClosedOnLeftEdge(): void
    {
        $string = [']'];
        $res = findMatch($string, '[', ']', 0, false);
        $this->assertEquals(-1, $res);
    }

    public function testItFindsForwardMatches(): void
    {
        $string = str_split('  [     ] ');
        $res = findMatch($string, '[', ']', 2, true);
        $this->assertEquals(8, $res);
    }

    public function testItFindsBackwardMatches(): void
    {
        $string = str_split('  [     ] ');
        $res = findMatch($string, '[', ']', 8, false);
        $this->assertEquals(2, $res);
    }

    public function testItFindsNestedForwardMatches(): void
    {
        $string = str_split(' [  [     ] ]  ');
        $res = findMatch($string, '[', ']', 1, true);
        $this->assertEquals(12, $res);
    }

    public function testItFindsNestedBackwardMatches(): void
    {
        $string = str_split(' [  [     ] ]  ');
        $res = findMatch($string, '[', ']', 12, false);
        $this->assertEquals(1, $res);
    }
}
