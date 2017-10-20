<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\StandardPhpStructuresTrait as TestSubject;
use PHPUnit_Framework_MockObject_MockObject;
use Exception;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class StandardPhpStructuresTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\StandardPhpStructuresTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance()
    {
        $mock = $this->getMockForTrait(static::TEST_SUBJECT_CLASSNAME);
        $mock->method('_createCustomFunctionException')
                ->will($this->returnCallback(function ($message, $code, $previous) {
                    return $this->createCustomFunctionException($message, $previous);
                }));
        $mock->method('_normalizeString')
                ->will($this->returnCallback(function ($string) {
                    return (string) $string;
                }));
        $mock->method('_callCustomFunction')
                ->will($this->returnCallback(function ($name, $args) {
                    return sprintf('%1$s|%2$s', $name, $args[0]);
                }));
        $mock->method('__')
                ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Creates a custom function exception for testing purposes.
     *
     * @since [*next-version*]
     *
     * @param string    $message  The error message.
     * @param Exception $previous The inner exception, if any.
     *
     * @return Exception The new exception.
     */
    public function createCustomFunctionException($message = '', $previous = null)
    {
        $mock = $this->mock('Exception')
                ->new($message, null, $previous);

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

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `_getOutputFunction()` result produces correct output when given string.
     *
     * @since [*next-version*]
     */
    public function testGetOutputFunctionString()
    {
        $data = uniqid('output-');
        $context = $this->_createContext();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
                ->method('_normalizeString');

        $function = $_subject->_getOutputFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        ob_start();
        $function($data);
        $output = ob_get_clean();
        $this->assertEquals($data, $output, 'The output function did not produce expected output');
    }

    /**
     * Tests that `_getOutputFunction()` result produces correct output when given context key.
     *
     * @since [*next-version*]
     */
    public function testGetOutputFunctionKey()
    {
        $key = uniqid('key-');
        $value = uniqid('value-');
        $data = [$key => $value];
        $context = $this->_createContext($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $function = $_subject->_getOutputFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        ob_start();
        $function($key);
        $output = ob_get_clean();
        $this->assertEquals($value, $output, 'The output function did not produce expected output');
    }

    /**
     * Tests that `_getTranslationFunction()` result produces correct result.
     *
     * @since [*next-version*]
     */
    public function testGetTranslationFunction()
    {
        $data = uniqid('string-');
        $context = $this->_createContext();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
                ->method('__');

        $function = $_subject->_getTranslationFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        $result = $function($data);
        $this->assertEquals($data, $result, 'The translation function did not produce expected result');
    }

    /**
     * Tests that `_getValueFunction()` result produces correct result.
     *
     * @since [*next-version*]
     */
    public function testGetValueFunction()
    {
        $key = uniqid('key-');
        $value = uniqid('value-');
        $data = [$key => $value];
        $context = $this->_createContext($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $context->expects($this->exactly(1))
                ->method('get');

        $function = $_subject->_getValueFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        $result = $function($key);
        $this->assertEquals($value, $result, 'The value function did not produce expected result');
    }

    /**
     * Tests that `_getCheckFunction()` result produces correct result when checking for an existing key.
     *
     * @since [*next-version*]
     */
    public function testGetCheckFunctionSuccess()
    {
        $key = uniqid('key-');
        $value = uniqid('value-');
        $data = [$key => $value];
        $context = $this->_createContext($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $context->expects($this->exactly(1))
                ->method('has');

        $function = $_subject->_getCheckFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        $result = $function($key);
        $this->assertTrue($result, 'The value function did not produce expected result');
    }

    /**
     * Tests that `_getCheckFunction()` result produces correct result when checking for a non-existing key.
     *
     * @since [*next-version*]
     */
    public function testGetCheckFunctionFailure()
    {
        $key = uniqid('key-');
        $context = $this->_createContext();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $context->expects($this->exactly(1))
                ->method('has');

        $function = $_subject->_getCheckFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        $result = $function($key);
        $this->assertFalse($result, 'The value function did not produce expected result');
    }

    /**
     * Tests that `_getCustomFunctionFunction()` result produces correct result when successful.
     *
     * @since [*next-version*]
     */
    public function testGetCustomFunctionFunctionSuccess()
    {
        $key = uniqid('key-');
        $value = uniqid('value-');
        $data = [$key => $value];
        $context = $this->_createContext($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
                ->method('_callCustomFunction')
                ->with($key, [$value]);

        $function = $_subject->_getCustomFunctionFunction($context);
        $this->assertInternalType('callable', $function, 'The function must be callable');

        $result = $function($key, $value);
        $this->assertEquals(sprintf('%1$s|%2$s', $key, $value), $result, 'The custom function did not produce expected result');
    }
}
