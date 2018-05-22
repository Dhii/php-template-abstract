<?php

namespace Dhii\Output;

use Exception as RootException;

/**
 * Functionality for isolating the scope of a file and restrict it to a set of variables.
 *
 * @since [*next-version*]
 */
trait IsolateFileScopeCapableTrait
{
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
    protected function _isolateFileScope($filePath)
    {
        $____file = $filePath;

        $fn = function (array $____vars) use ($____file) {
            extract($____vars, EXTR_SKIP);

            return require($____file);
        };
        $fn->bindTo(null);

        return $fn;
    }
}
