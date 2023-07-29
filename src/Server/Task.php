<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Swoolefony\SwooleBundle\Server\Task\TaskType;

class Task
{
    public function __construct(private readonly TaskType $taskType)
    {
    }

    public function getType(): TaskType
    {
        return $this->taskType;
    }
}
