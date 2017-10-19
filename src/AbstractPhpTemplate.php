<?php

namespace Dhii\Output;

use Psr\Container\ContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Abstract functionality for rendering a PHP template.
 *
 * @since [*next-version*]
 */
abstract class AbstractPhpTemplate extends AbstractBaseTemplate
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _renderWithContext($context)
    {
        $vars = $this->_getTemplateVars($context);

        return $this->_renderWithVars($context, $vars);
    }

    /**
     * Renders this template with with imported variables.
     *
     * @since [*next-version*]
     * 
     * @return string|Stringable The output.
     */
    abstract protected function _renderWithVars(ContainerInterface $context, array $vars = []);

    /**
     * Retrieves the variables that are to be imported into the scope of the template.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $context The current rendering context.
     *
     * @return array The variable map.
     */
    abstract protected function _getTemplateVars(ContainerInterface $context);
}
