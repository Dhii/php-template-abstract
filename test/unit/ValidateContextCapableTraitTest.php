<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\ValidateContextCapableTrait as TestSubject;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class ValidateContextCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\ValidateContextCapableTrait';

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
        $mock->method('__')
                ->will($this->returnArgument(0));
        $mock->method('_createValidationFailedException')
                ->will($this->returnCallback(function () {
                    return $this->createValidationFailedException();
                }));

        return $mock;
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string $className      Name of the class for the mock to extend.
     * @param string $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return object The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockForAbstractClass($paddingClassName);
    }

    /**
     * Creates a validation failed exception for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return ValidationFailedExceptionInterface
     */
    public function createValidationFailedException()
    {
        $mock = $this->mockClassAndInterfaces('Exception', ['Dhii\Validation\Exception\ValidationFailedExceptionInterface']);
        $mock->method('getValidationErrors')
                ->will($this->returnValue(null));
        $mock->method('getValidator')
                ->will($this->returnValue(null));
        $mock->method('getSubject')
                ->will($this->returnValue(null));

        $mock->method('getMessage')
                ->will($this->returnValue(null));
        $mock->method('getFile')
                ->will($this->returnValue(null));
        $mock->method('getLine')
                ->will($this->returnValue(null));
        $mock->method('getPrevious')
                ->will($this->returnValue(null));
        $mock->method('getCode')
                ->will($this->returnValue(null));
        $mock->method('getTrace')
                ->will($this->returnValue(null));
        $mock->method('getTraceAsString')
                ->will($this->returnValue(null));
        $mock->method('__toString')
                ->will($this->returnValue(null));

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
     * Tests that `_validateContext()` method works as expected when context is valid.
     *
     * @since [*next-version*]
     */
    public function testValidateContextSuccess()
    {
        $context = $this->_createContext();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->_validateContext($context);
        $this->assertTrue(true, 'A valid context failed validation');
    }

    /**
     * Tests that `_validateContext()` method works as expected when context is invalid.
     *
     * @since [*next-version*]
     */
    public function testValidateContextFailure()
    {
        $context = uniqid('context-');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('Dhii\Validation\Exception\ValidationFailedExceptionInterface');
        $_subject->_validateContext($context);
    }
}
