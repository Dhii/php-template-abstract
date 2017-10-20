<?php

namespace Dhii\Output;

use Traversable;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;

/**
 * Functionality for validating a context in the form of {@see ContainerInterface}.
 *
 * @since [*next-version*]
 */
trait ValidateContextCapableTrait
{
    /**
     * Validates the context.
     *
     * @since [*next-version*]
     *
     * @param mixed $context The context to validate.
     *
     * @throws ValidationFailedExceptionInterface If the context is invalid.
     */
    protected function _validateContext($context)
    {
        if ($context !== null && !($context instanceof ContainerInterface)) {
            throw $this->_createValidationFailedException(
                    $this->__('Invalid context'),
                    null,
                    $context,
                    [$this->__('Expected `ContainerInterface` or `null`')]);
        }
    }

    /**
     * Creates a new validation failed exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable                      $message  The error message, if any.
     * @param RootException|null                     $previous The inner exception, if any.
     * @param mixed                                  $subject  The validation subject, if any.
     * @param string[]|Stringable[]|Traversable|null $errors   The validation errors to associate with this instance, if any.
     *
     * @return ValidationFailedExceptionInterface The new exception.
     */
    abstract protected function _createValidationFailedException(
            $message = null,
            RootException $previous = null,
            $subject = null,
            $errors = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
