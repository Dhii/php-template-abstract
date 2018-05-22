<?php

namespace Dhii\Output;

use Dhii\Exception\InternalExceptionInterface;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use InvalidArgumentException;
use stdClass;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Traversable;

trait RenderWithVarsCapableFileTemplateTrait
{
    /**
     * Renders this template with specified variables.
     *
     * @since [*next-version*]
     *
     * @param array $vars The map of variable names to values to make available
     *                    in the template file scope.
     *
     * @throws InternalExceptionInterface If something goes wrong during rendering.
     *
     * @return string|Stringable The output.
     */
    protected function _renderWithVars(array $vars = [])
    {
        try {
            $path = $this->_getFilePath();
            $this->_validateTemplateFile($path);
            $include = $this->_isolateFileScope($path);

            return $this->_captureOutput($include, $vars);
        } catch (RootException $e) {
            throw $this->_createInternalException($e->getMessage(), null, $e);
        }
    }

    /**
     * Retrieves the file path associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable The path.
     */
    abstract protected function _getFilePath();

    /**
     * Isolates a scope of a PHP file, such that only variables from a specific map are accessible inside.
     *
     * @since [*next-version*]
     *
     * @param string $filePath The path to the file, the scope of which to isolate.
     *
     * @throws RootException If a problem occurs while isolating.
     *
     * @return callable The callable which isolates file scope.
     */
    abstract protected function _isolateFileScope($filePath);

    /**
     * Invokes the given callable, and returns the output as a string.
     *
     * @since [*next-version*]
     *
     * @param callable                        $callable The callable that may produce output.
     * @param array|stdClass|Traversable|null $args     The arguments to invoke the callable with. Defaults to empty array.
     *
     * @throws InvalidArgumentException If the callable or the args list are invalid.
     * @throws RootException            If a problem occurs.
     *
     * @return string The output.
     */
    abstract protected function _captureOutput(callable $callable, $args = null);

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
    abstract protected function _validateTemplateFile($path);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Creates a new Internal exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return InternalExceptionInterface The new exception.
     */
    abstract protected function _createInternalException($message = null, $code = null, RootException $previous = null);
}
