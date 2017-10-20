<?php

namespace Dhii\Output;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;

/**
 * Functionality for template file validation.
 *
 * @since [*next-version*]
 */
trait ValidateTemplateFileCapableTrait
{
    /**
     * Validates a template file.
     *
     * To be valid, the file identified by the path must exist and be readable.
     *
     * @since [*next-version*]
     *
     * @param string $path The absolute file path.
     *
     * @throws ValidationFailedExceptionInterface If the file is invalid.
     */
    protected function _validateTemplateFile($path)
    {
        $errors = [];

        if (!file_exists($path)) {
            array_push($errors, $this->__('File does not exist'));
        } elseif (!is_readable($path)) {
            array_push($errors, $this->__('File is not readable'));
        }

        if (!empty($errors)) {
            throw $this->_createValidationFailedException(
                    $this->__('Invalid template file'), null, $path, $errors);
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
