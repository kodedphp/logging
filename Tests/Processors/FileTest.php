<?php

namespace Koded\Logging\Processors;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $dir;

    /**
     * @dataProvider dataMessage
     * @param $message
     */
    public function testFormatting($message)
    {
        $subdirectory = date('Y/m');
        $file = date('d') . '.log';

        $processor = new File(['dir' => $this->dir->url()]);
        $processor->update([$message]);

        $this->assertTrue($this->dir->hasChild($subdirectory));

        $content = $this->dir->getChild($subdirectory . DIRECTORY_SEPARATOR . $file)->getContent();
        $this->assertContains('[1234567890] DEBUG: Hello', $content);
    }

    /**
     * @dataProvider dataMessage
     * @param $message
     */
    public function testWhenDirectoryDoesNotExist($message)
    {
        $dir = $this->dir->url() . '/nonexistent';

        $this->expectException(FileProcessorException::class);
        $this->expectExceptionMessage('Log directory "' . $dir . '/" must exist');

        $processor = new File(['dir' => $dir]);
        $processor->update([$message]);
    }

    /**
     * @dataProvider dataMessage
     * @param $message
     */
    public function testWhenDirectoryIsNotWritable($message)
    {
        $dir = $this->dir->url();

        $this->expectException(FileProcessorException::class);
        $this->expectExceptionMessage('Log directory "' . $dir . '/" must be writable');

        vfsStreamWrapper::getRoot()->chmod(0400);
        $processor = new File(['dir' => $dir]);
        $processor->update([$message]);
    }

    public function dataMessage()
    {
        return [
            [
                [
                    'level' => -1,
                    'levelname' => 'DEBUG',
                    'message' => 'Hello',
                    'timestamp' => 1234567890
                ]
            ]
        ];
    }

    protected function setUp()
    {
        $this->dir = vfsStream::setup();
    }
}
