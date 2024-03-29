<?php

declare(strict_types=1);

namespace Brainfudge;

final class StdinScanner implements ScannerInterface
{
    public function getChar(): string
    {
        return fgetc(STDIN);
    }
}
