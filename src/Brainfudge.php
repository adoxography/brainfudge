<?php

declare(strict_types=1);

namespace Brainfudge;

/**
 * Interprets Brainfudge programs
 */
final class Brainfudge
{
    /** @var list<string> $program  The program to interpret */
    public array $program;

    /** @var array $registers  The program's registers */
    public array $registers = [ 0 ];

    /** @var int $registerIdx  The index of the current register */
    private int $registerIdx = 0;

    /** @var int $programIdx  The index of the current command */
    private int $programIdx = 0;

    /** @var string $output  The program's output */
    private string $output = '';

    /** @var ScannerInterface $scanner  Object to retrieve user input */
    private ScannerInterface $scanner;

    /**
     * @var list<string, string> CALL_TABLE  Map from brainfudge symbol to
     *                                       internal handler method
     */
    private const CALL_TABLE = [
        '+' => 'plus',
        '-' => 'minus',
        '>' => 'goNext',
        '<' => 'goPrev',
        '.' => 'print',
        ',' => 'read',
        '[' => 'handleOpenBracket',
        ']' => 'handleCloseBracket'
    ];

    /**
     * Runs a brainfudge program and returns its output
     *
     * @param string $program  The text of the brainfudge program
     * @return string  The output of the program
     */
    public static function run(string $program): string
    {
        $interpreter = new self($program);
        return $interpreter->process();
    }

    /**
     * Initializes the interpreter
     *
     * @param string $program             The program to interpret
     * @param ?ScannerInterface $scanner  An object to get user input
     */
    public function __construct(string $program, ?ScannerInterface $scanner = null)
    {
        $this->program = str_split($program);
        $this->scanner = $scanner ?: new StdinScanner();
    }

    /**
     * Processes the internal program
     *
     * @return string  The output of the program
     */
    public function process(): string
    {
        while ($this->programIdx < count($this->program)) {
            $curr = $this->program[$this->programIdx];

            if (array_key_exists($curr, self::CALL_TABLE)) {
                $func = self::CALL_TABLE[$curr];
                $this->programIdx = $this->$func();
            } else {
                $this->programIdx++;
            }
        }

        return $this->output;
    }

    /**
     * Increments the current register, ensuring that it wraps back to 0 at 256
     *
     * @return int  The index of the next command
     */
    private function plus(): int
    {
        $this->registers[$this->registerIdx]++;
        $this->registers[$this->registerIdx] %= 256;

        return $this->programIdx + 1;
    }

    /**
     * Decrements the current register, ensuring that it wraps back to 256 at 0
     *
     * @return int  The index of the next command
     */
    private function minus(): int
    {
        $this->registers[$this->registerIdx] += 255;
        $this->registers[$this->registerIdx] %= 256;

        return $this->programIdx + 1;
    }

    /**
     * Moves the register pointer to the next register, adding a new register
     * if necessary
     *
     * @return int  The index of the next command
     */
    private function goNext(): int
    {
        $this->registerIdx++;

        if (count($this->registers) == $this->registerIdx) {
            $this->registers[] = 0;
        }

        return $this->programIdx + 1;
    }

    /**
     * Moves the register pointer to the previous register
     *
     * @return int  The index of the next command
     */
    private function goPrev(): int
    {
        $this->registerIdx--;

        if ($this->registerIdx < 0) {
            throw new \OutOfBoundsException();
        }

        return $this->programIdx + 1;
    }

    /**
     * Adds the char code of the current register to the program's output
     *
     * @return int  The index of the next command
     */
    private function print(): int
    {
        $this->output .= chr($this->registers[$this->registerIdx]);
        return $this->programIdx + 1;
    }

    /**
     * Retrieves a byte of input from the $scanner, sets the current register
     * to the value of its ASCII value
     *
     * @return int  The index of the next command
     */
    private function read(): int
    {
        $this->registers[$this->registerIdx] = ord($this->scanner->getChar());
        return $this->programIdx + 1;
    }

    /**
     * If the current register holds a 0, goes to the command after the matching
     * ]
     *
     * @return int  The index of the next command
     */
    private function handleOpenBracket(): int
    {
        if (($next = $this->findMatch(true)) < 0) {
            throw new \LogicException();
        }

        if ($this->registers[$this->registerIdx] == 0) {
            return $next + 1;
        }

        return $this->programIdx + 1;
    }

    /**
     * If the current register doesn't hold a 0, goes back to the matching [
     *
     * @return int  The index of the next command
     */
    private function handleCloseBracket(): int
    {
        if (($prev = $this->findMatch(false)) < 0) {
            throw new \LogicException();
        }

        if ($this->registers[$this->registerIdx] != 0) {
            return $prev;
        }

        return $this->programIdx + 1;
    }

    /**
     * Finds the matching bracket
     *
     * @param bool $fwd  Whether the search should be done forward or backward
     * @return int  The index of the matching bracket, or -1 if none exists
     */
    private function findMatch(bool $fwd): int
    {
        return findMatch($this->program, '[', ']', $this->programIdx, $fwd);
    }
}
