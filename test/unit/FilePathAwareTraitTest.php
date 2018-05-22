<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\FilePathAwareTrait as TestSubject;
use Dhii\Util\String\StringableInterface as Stringable;
use PHPUnit_Framework_MockObject_MockObject;
use InvalidArgumentException;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class FilePathAwareTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\FilePathAwareTrait';

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
        $mock->method('_createInvalidArgumentException')
                ->will($this->returnCallback(function ($message = null) {
                    return $this->createInvalidArgumentException($message);
                }));
        $mock->method('__')
                ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Creates a validation failed exception for testing purposes.
     *
     * @since [*next-version*]
     *
     * @param string $message The error message.
     *
     * @return InvalidArgumentException
     */
    public function createInvalidArgumentException($message = '')
    {
        $mock = $this->mock('InvalidArgumentException')
                ->new($message);

        return $mock;
    }

    /**
     * Creates a stringable.
     *
     * @since [*next-version*]
     *
     * @param string $string The string that the stringable should represent.
     *
     * @return Stringable The new stringable
     */
    public function createStringable($string = '')
    {
        $mock = $this->getMock('Dhii\Util\String\StringableInterface');
        $mock->method('__toString')
                ->will($this->returnCallback(function () use ($string) {
                    return $string;
                }));

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
     * Tests that `_setFilePath()` method accepts a stringable, and `_getFilePath()` returns it.
     *
     * @since [*next-version*]
     */
    public function testSetGetFilePathStringable()
    {
        $data = $this->createStringable(uniqid('file-path-'));
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->_setFilePath($data);
        $this->assertSame($data, $_subject->_getFilePath(), 'File path returned not same as file path set');
    }

    /**
     * Tests that `_setFilePath()` method accepts a string, and `_getFilePath()` returns it.
     *
     * @since [*next-version*]
     */
    public function testSetGetFilePathString()
    {
        $data = uniqid('file-path-');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->_setFilePath($data);
        $this->assertEquals($data, $_subject->_getFilePath(), 'File path returned not same as file path set');
    }

    /**
     * Tests that `_setFilePath()` method accepts `null`, and `_getFilePath()` returns it.
     *
     * @since [*next-version*]
     */
    public function testSetGetFilePathNull()
    {
        $data = null;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->_setFilePath($data);
        $this->assertEquals($data, $_subject->_getFilePath(), 'File path returned not same as file path set');
    }

    /**
     * Tests that `_setFilePath()` method rejects a non-stringable object.
     *
     * @since [*next-version*]
     */
    public function testSetGetFilePathObject()
    {
        $data = new \stdClass();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_setFilePath($data);
    }
}
