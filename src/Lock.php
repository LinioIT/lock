<?php

declare(strict_types=1);

namespace Linio\Lock;

class Lock
{
    /**
     * Lock path. If not set, the system temporary directory is used.
     *
     * @var string
     */
    protected $path;

    /**
     * Lock name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @param string $name
     * @param Process $process
     */
    public function __construct(string $name, Process $process = null)
    {
        $this->name = $name;

        if (!($process instanceof Process)) {
            $this->process = new Process();
        } else {
            $this->process = $process;
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        if (empty($this->path)) {
            return sys_get_temp_dir();
        }

        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->getName() . '.lock';
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        $locked = file_exists($this->getFileName());

        if ($locked) {
            $pid = trim(file_get_contents($this->getFileName()));

            if (!$this->process->isApplicationProcess($pid)) {
                $this->release();

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @throws LockException
     */
    public function acquire()
    {
        if (file_put_contents($this->getFileName(), getmypid()) === false) {
            throw new LockException('Lock file could not be created.');
        }
    }

    /**
     * @throws LockException
     */
    public function release()
    {
        if (!file_exists($this->getFileName())) {
            return;
        }

        if (!unlink($this->getFileName())) {
            throw new LockException('Lock file could not be removed.');
        }
    }

    /**
     * @throws LockException
     */
    public function forceRelease()
    {
        if (!$this->isLocked()) {
            return;
        }

        $pid = (int) trim(file_get_contents($this->getFileName()));

        if (!$this->process->isApplicationProcess($pid)) {
            $this->release();

            return;
        }

        if (!posix_kill($pid, SIGKILL)) {
            throw new LockException('Unable to kill the running process.');
        }

        $this->release();
    }
}
