<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Dhii\Validation\Exception\ValidationFailedException;

/**
 * Abstract functionality for PHTML templates.
 *
 * @since [*next-version*]
 */
abstract class AbstractPhpFileTemplate extends AbstractPhpTemplate
{
    /**
     * Renders this template with with imported variables.
     *
     * Inside the closure, the only available variables are:
     * - `$__vars` - system variable reserved to hold the variable name to value
     * map, which will have local variables created from.
     * - `$__path` - system variable reserved to hold the path to the template
     * file itself.
     * - Anything that has a key in the `$vars` param.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The context to use for rendering.
     * @param array              $vars    The map of variable names to values to make available
     *                                    in the template file scope.
     *
     * @return string|Stringable The output.
     */
    protected function _renderWithVars(ContainerInterface $context, array $vars = [])
    {
        $path = $this->_getFilePath($context);

        try {
            $this->_validateTemplateFile($path);
        } catch (ValidationFailedException $e) {
            throw $this->_createTemplateException($e->getMessage(), null, $e, $context);
        }

        // Isolate scope
        $include = function ($__path, $__vars) {
            extract($__vars, EXTR_SKIP);
            include $__path;
        };
        // Prevent temptation by making `$this` unavailable in the template.
        $include = $include->bindTo(null);

        ob_start();
        call_user_func_array($include, [$path, $vars]);
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Retrieves the path to the template file.
     *
     * @since [*next-version*]
     *
     * @return string The path to the template file.
     */
    abstract protected function _getFilePath();

    /**
     * Validates a PHTML template file.
     *
     * @since [*next-version*]
     *
     * @throws ValidationFailedException If the template file is not retrievable.
     */
    abstract protected function _validateTemplateFile($path);
}
