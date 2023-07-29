<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Handler;

use Swoole\Server;
use Swoole\Server\Task as SwooleTask;
use Swoolefony\SwooleBundle\Server\Task;
use Swoolefony\SwooleBundle\Server\Task\Dispatcher;

readonly class TaskHandler
{
    public function __construct(private Dispatcher $dispatcher)
    {
    }

    public function __invoke(
        Server $server,
        SwooleTask $swooleTask,
    ): void {
        /** @var Task $task */
        $task = $swooleTask->data;

        $this->dispatcher->dispatch($task);
    }
}
