<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Task;

use Swoolefony\SwooleBundle\Server\Task;
use Swoolefony\SwooleBundle\Server\Task\Handler\TaskHandlerInterface;

readonly class Dispatcher
{
    /**
     * @param iterable<TaskHandlerInterface&callable> $taskHandlers
     */
    public function __construct(private iterable $taskHandlers)
    {
    }

    public function dispatch(Task $task): void
    {
        foreach ($this->taskHandlers as $taskHandler) {
            if ($taskHandler->supports() === $task->getType()) {
                $taskHandler($task);
            }
        }
    }
}
