<?php
/**
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Tests\Translation;

use ManuelAguirre\Bundle\TranslationBundle\Translation\CacheRemover;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CacheRemoverTest
 *
 * @author Manuel Aguirre maguirre@optimeconsulting.com
 */
class CacheRemoverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheRemover
     */
    private $cacheRemover;
    private $filesystem;
    private $cacheDir = __DIR__;
    private $transFolder = '/translations/';

    protected function setUp()
    {
        $this->filesystem = $this->prophesize(Filesystem::class);
        $this->cacheRemover = new CacheRemover(
            $this->filesystem->reveal(),
            $this->cacheDir
        );
    }

    public function testVerifyIfCacheExists()
    {
        $this->filesystem->exists(
            $this->cacheDir.$this->transFolder
        )->shouldBeCalled()->willReturn(false);
        $this->filesystem->remove(Argument::any())->willReturn();

        $this->cacheRemover->clear();
    }

    public function testRemoveWhenFileExists()
    {
        $this->filesystem->exists($this->cacheDir.$this->transFolder)->willReturn(true);
        $this->filesystem->remove($this->cacheDir.$this->transFolder)->shouldBeCalled();

        $return = $this->cacheRemover->clear();

        self::assertTrue($return);
    }

    public function testNotRemoveWhenFileDoesNotExists()
    {
        $this->filesystem->exists($this->cacheDir.$this->transFolder)->willReturn(false);
        $this->filesystem->remove($this->cacheDir.$this->transFolder)->shouldNotBeCalled();

        $return = $this->cacheRemover->clear();

        self::assertNull($return);
    }

    public function testNotThrowExceptionInFailedRemoval()
    {
        $this->filesystem->exists($this->cacheDir.$this->transFolder)->willReturn(true);
        $this->filesystem->remove($this->cacheDir.$this->transFolder)->willThrow(IOException::class);

        $return = $this->cacheRemover->clear();
        self::assertFalse($return);
    }
}
