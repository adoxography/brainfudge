<?php

declare(strict_types=1);

/**
 * Finds the matching counterpart of a character, taking nesting into account
 *
 * @param list<string> $haystack  The characters to search within
 * @param string $left            The character that appears on the left
 * @param string $right           The character that appears on the right
 * @param int $start              The position to start searching from
 * @param bool $fwd               If true, the search looks from $start to the
 *                                end of $haystack for the matching $right
 *                                character. If false, the search looks from
 *                                $start to the start of $haystack for the
 *                                matching $left character.
 * @return int  The index of the matching character, or -1 if there is no such
 *              character.
 */
function findMatch(
    array $haystack,
    string $left,
    string $right,
    int $start,
    bool $fwd
): int {
    // Set up the range that needs to be searched based on the direction
    $range = $fwd ?
        range($start + 1, count($haystack) - 1) :
        array_reverse(range(0, $start - 1));

    // Define the characters we're searching for: $self is the one we're looking
    // for a match to; $other is the character we're looking for.
    [$self, $other] = $fwd ? [$left, $right] : [$right, $left];

    $seen = 0;

    foreach ($range as $i) {
        // Bail if there are no more characters
        if ($i < 0 || $i >= count($haystack)) {
            return -1;
        }

        $char = $haystack[$i];

        // Encountering the $self character means we've entered a deeper nest
        if ($char == $self) {
            $seen++;
        }

        // If we encounter the $other character, either pull out of the nest
        // or return the index
        if ($char == $other) {
            if ($seen == 0) {
                return $i;
            }
            $seen--;
        }
    }

    // The character was not found
    return -1;
}
