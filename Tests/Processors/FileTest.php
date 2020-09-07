<?php

namespace Koded\Logging\Tests\Processors;

use Koded\Logging\Processors\{File, FileProcessorException};
use org\bovigo\vfs\{vfsStream, vfsStreamDirectory, vfsStreamWrapper};
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $dir;

    public function test_update()
    {
        $subdirectory = date('Y/m');
        $file = date('d') . '.log';

        $processor = new File(['dir' => $this->dir->url()]);
        $processor->update([
            [
                'level'     => -1,
                'levelname' => 'DEBUG',
                'message'   => 'Test 1',
                'timestamp' => 1234567890
            ],
            [
                'level'     => -1,
                'levelname' => 'DEBUG',
                'message'   => 'Test 2',
                'timestamp' => 1234567891
            ]
        ]);

        $this->assertSame('', $processor->formatted());
        $this->assertTrue($this->dir->hasChild($subdirectory));

        $content = $this->dir->getChild($subdirectory . DIRECTORY_SEPARATOR . $file)->getContent();
        $this->assertStringContainsString("1234567891 [DEBUG]: Test 2\n", $content);
    }

    public function test_when_directory_does_not_exist()
    {
        $dir = $this->dir->url() . '/nonexistent';

        $this->expectException(FileProcessorException::class);
        $this->expectExceptionMessage('Log directory "' . $dir . '" must exist');

        $processor = new File(['dir' => $dir]);
        $processor->update([]);
    }

    public function test_when_directory_is_not_writable()
    {
        $dir = $this->dir->url();

        $this->expectException(FileProcessorException::class);
        $this->expectExceptionMessage('Log directory "' . $dir . '" must be writable');

        vfsStreamWrapper::getRoot()->chmod(0400);
        $processor = new File(['dir' => $dir]);
        $processor->update([]);
    }

    protected function setUp(): void
    {
        $this->dir = vfsStream::setup();
    }
}
