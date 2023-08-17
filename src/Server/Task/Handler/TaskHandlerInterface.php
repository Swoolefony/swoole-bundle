<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Task\Handler;

use Swoolefony\SwooleBundle\Server\Task\TaskType;

interface TaskHandlerInterface
{
    public function supports(): TaskType;
}
