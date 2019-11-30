<?php

declare(strict_types=1);

namespace Tests\Util;

use Brainfudge\ScannerInterface;

class MockReader implements ScannerInterface
{
    public string $value;
    public int $index = 0;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getChar(): string
    {
        return $this->value[$this->index++];
    }
}
