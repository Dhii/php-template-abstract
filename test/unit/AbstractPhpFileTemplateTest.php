<?php

namespace Dhii\Output\UnitTest;

use Xpmock\TestCase;
use Dhii\Output\AbstractPhpFileTemplate as TestSubject;
use Psr\Container\ContainerInterface;
use PHPUnit_Framework_MockObject_MockBuilder;
use VirtualFileSystem\FileSystem;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractPhpFileTemplateTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Output\AbstractPhpFileTemplate';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    public function createInstance($filePath = '', array $templateVars = [])
    {
        $mock = $this->getMockForAbstractClass(static::TEST_SUBJECT_CLASSNAME);
        $mock->method('_getTemplateVars')
                ->will($this->returnValue($templateVars));
        $mock->method('_getFilePath')
                ->will($this->returnValue($filePath));

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
     * Creates a new populated file system.
     *
     * @since [*next-version*]
     *
     * @return FileSystem The new file system.
     */
    public function createFileSystem($path, $body)
    {
        $fs = new FileSystem();

        $fs->createStructure([
            $path => $body,
        ]);

        return $fs;
    }

    /**
     * Retrieves the file path for the template file.
     *
     * @since [*next-version*]
     *
     * @return string The path.
     */
    protected function _getTemplateFilePath()
    {
        return 'my-template.phtml';
    }

    /**
     * Retrieves the body of the template.
     *
     * @since [*next-version*]
     *
     * @param string $varName The name of the variable that the template will output.
     *
     * @return string The template body, in PHTML format.
     */
    protected function _getTemplateBody($varName)
    {
        return <<<EOF
<?php echo \${$varName} ?>
EOF;
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
     * Tests that `_renderWithVars()` method works as expected.
     *
     * @since [*next-version*]
     */
    public function testRenderWithVars()
    {
        $output = uniqid('output-');
        $context = $this->_createContext();
        $varName = uniqid('v');
        $vars = [$varName => $output];
        $path = $this->_getTemplateFilePath();
        $body = $this->_getTemplateBody($varName);
        $fs = $this->createFileSystem($path, $body);
        $uri = $fs->path($path);
        $subject = $this->createInstance($uri, $vars);
        $subject->expects($this->exactly(1))
                ->method('_validateTemplateFile')
                ->with($this->equalTo($uri));
        $_subject = $this->reflect($subject);

        $result = $_subject->_renderWithVars($context, $vars);
        $this->assertEquals($output, $result, 'Subject did not produce expected output');
    }
}
