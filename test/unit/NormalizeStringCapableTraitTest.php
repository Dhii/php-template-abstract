<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\NormalizeStringCapableTrait as TestSubject;
use Dhii\Util\String\StringableInterface as Stringable;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeStringCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\NormalizeStringCapableTrait';

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
     * Tests that `_normalizeString()` method works as expected when normalizing a stringable object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringStringable()
    {
        $data = uniqid('string-');
        $stringable = $this->createStringable($data);
        $stringable->expects($this->exactly(1))
                ->method('__toString');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($stringable);
        $this->assertEquals($data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a scalar integer.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringInteger()
    {
        $data = rand(1, 100);
        $stringable = $this->createStringable($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($stringable);
        $this->assertEquals((string) $data, $result, 'The stringable was not normalized correctly');
    }
}
