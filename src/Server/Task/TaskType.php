<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server\Task;

enum TaskType: string
{
    case Tick = 'swoolefony.tick';
}
