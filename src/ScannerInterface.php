<?php

declare(strict_types=1);

namespace Brainfudge;

interface ScannerInterface
{
    public function getChar(): string;
}
