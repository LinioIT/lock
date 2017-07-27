<?php

declare(strict_types=1);

namespace Linio\Lock;

class Process
{
    /**
     * @var int
     */
    protected $pid;

    public function __construct()
    {
        $this->pid = getmypid();
    }

    /**
     * @return string
     */
    public function getApplicationName(): string
    {
        return basename($_SERVER['argv'][0]);
    }

    /**
     * Check if the given pid is an instance of the application.
     *
     * @param int $pid
     *
     * @return bool
     */
    public function isApplicationProcess($pid): bool
    {
        // This is a workaround to avoid the grep processs to be match
        $grepProofPid = preg_replace('/([\d]+)(\d)$/', '$1[$2]', $pid);
        $command = sprintf('ps aux | egrep \'%s.*%s( )?\'', $grepProofPid, $this->getApplicationName());

        exec($command, $output, $result);

        return $result === 0;
    }
}
