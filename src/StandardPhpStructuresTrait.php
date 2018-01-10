<?php

namespace Dhii\Output;

use DomainException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Extended functionality for template structures and vars.
 *
 * @since [*next-version*]
 */
trait StandardPhpStructuresTrait
{
    /**
     * Retrieves the function to be used for outputting values.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The current rendering context.
     *
     * @return callable The output function.
     *                  This function MUST have the following signature:
     *
     * output(mixed $subject)
     *
     * Where
     * - `$subject` is the subject to output. If it is a string, and the context
     * has a matching value, then it will be used as a key to retrieve that
     * value from the context. Otherwise, it will be normalized to string
     * and output.
     */
    protected function _getOutputFunction(ContainerInterface $context)
    {
        return function ($subject) use ($context) {
            if (is_string($subject) && $context->has($subject)) {
                $subject = $context->get($subject);
            }

            echo $this->_normalizeString($subject);
        };
    }

    /**
     * Retrieves the function to be used for translation.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The current rendering context.
     *
     * @return callable The translation function.
     *                  This function MUST have the following signature:
     *
     * translate(string $string, array $args): string
     *
     * Where
     * - `$string` is the subject of translation, and can be any string.
     * After translation, any `sprintf()` style placeholders will be interpolated.
     * - `$args` is the optional array of arguments for placeholder interpolation.
     */
    protected function _getTranslationFunction(ContainerInterface $context)
    {
        return function ($string, $args = []) {
            return $this->__($string, $args);
        };
    }

    /**
     * Retrieves the function to be used for getting values inside the template.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The current rendering context.
     *
     * @return callable The value-retrieving function.
     *                  This function must have the following signature:
     *
     *  ```
     *  value(string $key, [$default = null]): mixed
     *  ```
     *
     *  The function MUST attempt to retrieve a value by key from the container.
     *  If the value does not exist, it SHOULD return the value specified as default.
     *  In some cases, such as for debugging purposes, the default value MAY
     *  be overridden.
     */
    protected function _getValueFunction(ContainerInterface $context)
    {
        return function ($k, $default = null) use ($context) {
            try {
                return $context->get($k);
            } catch (NotFoundExceptionInterface $e) {
                return $default;
            }
        };
    }

    /**
     * Retrieves a function that checks if a value exists in the context.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The current rendering context.
     *
     * @return callable The value-checking function.
     *                  This function must have the following signature:
     *
     *  ```
     *  check(string $key): bool
     *  ```
     *
     *  The function MUST NOT attempt to retrieve a value by key from the container.
     */
    protected function _getCheckFunction(ContainerInterface $context)
    {
        return function ($k) use ($context) {
            return $context->has($k);
        };
    }

    /**
     * @param ContainerInterface $context
     *
     * @return callable The custom function-calling function.
     *                  This function MUST have the following signature:
     *
     * ```
     * custom(string $code, [$argN...]): mixed
     * ```
     *
     * Where
     * - `$code` is the code of the function to call.
     * - All other args will be passed to the custom function in that order.
     */
    protected function _getCustomFunctionFunction(ContainerInterface $context)
    {
        return function ($code) {
            $args = func_get_args();
            array_shift($args);

            try {
                return $this->_callCustomFunction($code, $args);
            } catch (RootException $ex) {
                throw $this->_createCustomFunctionException($this->__('Problem calling custom function'), null, $ex, $code);
            }
        };
    }

    /**
     * Calls a custom function by its code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code of the function to call.
     * @param array             $args The arguments to pass to the function.
     *
     * @throws DomainException If the function is not callable
     *
     * @return mixed The result of the function call.
     */
    abstract protected function _callCustomFunction($code, $args = []);

    /**
     * Creates a new function that indicates a problem related to a custom function.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message      The error message, if any.
     * @param int|null               $code         The error code, if any.
     * @param RootException|null     $previous     The inner exception for chaining, if any.
     * @param string|Stringable|null $functionCode Code The code of the custom function, if any.
     *
     * @return RootException The new exception.
     */
    abstract protected function _createCustomFunctionException($message = null, $code = null, RootException $previous = null, $functionCode = null);

    /**
     * Normalize a value to its string representation.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value to normalize.
     *
     * @return string The string representation of the value.
     */
    abstract protected function _normalizeString($value);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
