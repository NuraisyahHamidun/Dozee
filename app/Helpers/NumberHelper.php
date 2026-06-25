<?php
namespace App\Helpers;

/**
 * Helper for number related utilities.
 */
class NumberHelper
{
    /**
     * Get the minimum non‑negative (>= 0) value from an array.
     *
     * - Removes any values < 0.
     * - If no values >= 0 are present, returns 0.
     *
     * @param  array<int|float> $numbers  List of numbers to evaluate
     * @return int|float                Minimum value >= 0 or 0 if none.
     */
    public static function minNonNegative(array $numbers)
    {
        // Filter only numbers that are >= 0
        $filtered = array_filter($numbers, fn($n) => $n >= 0);

        // If the filtered list is empty, return 0
        if (empty($filtered)) {
            return 0;
        }

        // Return the smallest value from the filtered list
        return min($filtered);
    }
}
?>
