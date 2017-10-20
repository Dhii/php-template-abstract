<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for storing and retrieving a file path.
 *
 * @since [*next-version*]
 */
trait FilePathAwareTrait
{
    /**
     * The file path.
     *
     * @var string|Stringable
     */
    protected $filePath;

    /**
     * Retrieves the file path associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable The path.
     */
    protected function _getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Assigns a file path to this instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $path The path to set.
     */
    public function _setFilePath($path)
    {
        if (!is_string($path) && $path !== null && !($path instanceof Stringable)) {
            throw $this->_createInvalidArgumentException($this->__('Invalid path'), null, null, $path);
        }

        $this->filePath = $path;
    }

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
