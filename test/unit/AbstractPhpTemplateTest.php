<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\AbstractPhpTemplate as TestSubject;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_MockObject_MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractPhpTemplateTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\AbstractPhpTemplate';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    public function createInstance($output = '', array $templateVars = [])
    {
        $mock = $this->getMockForAbstractClass(static::TEST_SUBJECT_CLASSNAME);
        $mock->method('_renderWithVars')
                ->will($this->returnValue($output));
        $mock->method('_getTemplateVars')
                ->will($this->returnValue($templateVars));

        return $mock;
    }

    /**
     * Creates a new context.
     *
     * @since [*next-version*]
     *
     * @param array $data The data map for the context.
     *
     * @return ContainerInterface The new context.
     */
    protected function _createContext($data = [])
    {
        $mock = $this->mock('Psr\Container\ContainerInterface')
                ->get(function ($key) use ($data) {
                    return isset($data[$key]) ? $data[$key] : null;
                })
                ->has(function ($key) use ($data) {
                    return isset($data[$key]);
                })
                ->new();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf('Dhii\Output\TemplateInterface', $subject, 'Subject does not implement required interface');
    }

    /**
     * Tests that `_renderWithContext()` method works as expected.
     *
     * @since [*next-version*]
     */
    public function testRenderWithContext()
    {
        $vars = ['var1' => 123];
        $context = $this->_createContext();
        $subject = $this->createInstance('', $vars);
        $subject->expects($this->exactly(1))
                ->method('_renderWithVars')
                ->with($this->equalTo($context), $this->equalTo($vars));
        $subject->expects($this->exactly(1))
                ->method('_getTemplateVars')
                ->with($this->equalTo($context));
        $_subject = $this->reflect($subject);

        $result = $_subject->_renderWithContext($context);
        echo $result;
    }
}
