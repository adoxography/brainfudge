<?php

declare(strict_types=1);

function findMatch(
    array $haystack,
    string $left,
    string $right,
    int $start,
    bool $fwd
): int {
    $range = $fwd ?
        range($start + 1, count($haystack) - 1) :
        array_reverse(range(0, $start - 1));

    [$self, $other] = $fwd ? [$left, $right] : [$right, $left];

    $seen = 0;

    foreach ($range as $i) {
        if ($i < 0 || $i >= count($haystack)) {
            return -1;
        }

        $char = $haystack[$i];

        if ($char == $self) {
            $seen++;
        }

        if ($char == $other) {
            if ($seen == 0) {
                return $i;
            }
            $seen--;
        }
    }

    return -1;
}
