<?php

declare(strict_types=1);

namespace spec\Linio\Lock;

use Linio\Lock\Lock;
use Linio\Lock\Process;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;

class LockSpec extends ObjectBehavior
{
    private $lockName = 'test';
    private $path = 'vfs://test';

    public function let(Process $process)
    {
        vfsStream::setup('test');

        $process->getApplicationName()->willReturn($this->lockName);

        $this->beConstructedWith($this->lockName, $process);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Lock::class);
    }

    public function it_should_fallback_to_system_tmp_dir_when_path_is_not_set()
    {
        $this->getPath()->shouldReturn(sys_get_temp_dir());
    }

    public function it_should_use_defined_path_when_explicitly_set()
    {
        $this->setPath($this->path);
        $this->getPath()->shouldReturn($this->path);
    }

    public function it_should_return_the_lock_filename()
    {
        $this->getFileName()->shouldReturn(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->lockName . '.lock');
    }

    public function it_should_not_be_locked()
    {
        $this->isLocked()->shouldReturn(false);
    }

    public function it_should_be_locked($process)
    {
        $pid = '1234';
        file_put_contents(sprintf('%s/%s.lock', $this->path, $this->lockName), $pid);

        $process->isApplicationProcess($pid)->willReturn(true)->shouldBeCalled();
        $this->setPath($this->path);

        $this->isLocked()->shouldReturn(true);
    }

    public function it_should_not_be_locked_if_process_is_not_from_this_application($process)
    {
        $pid = '1234';
        file_put_contents(sprintf('%s/%s.lock', $this->path, $this->lockName), $pid);

        $process->isApplicationProcess($pid)->willReturn(false)->shouldBeCalled();
        $this->setPath($this->path);

        $this->isLocked()->shouldReturn(false);
    }
}
