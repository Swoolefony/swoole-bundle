<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole;

use Swoole\Process;

class ProcessTerminator
{
    private const SIGTERM = 15;

    private const SIGKILL = 9;

    public function stop(int $pid): bool
    {
        return (bool) Process::kill(
            $pid,
            self::SIGTERM,
        );
    }

    public function forceStop(int $pid): bool
    {
        return (bool) Process::kill(
            $pid,
            self::SIGKILL,
        );
    }
}
