<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for string normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeStringCapableTrait
{
    /**
     * Normalizes a value to its string representation.
     *
     * @since [*next-version*]
     *
     * @param Stringable|mixed $subject The value to normalize to string.
     *
     * @return string The string that resulted from normalization.
     */
    protected function _normalizeString($subject)
    {
        if ($subject instanceof Stringable) {
            return $subject->__toString();
        }

        return (string) $subject;
    }
}
