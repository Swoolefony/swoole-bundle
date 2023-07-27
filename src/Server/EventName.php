<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

enum EventName: string
{
    case Request = 'request';

    case Start = 'start';

    case Shutdown = 'shutdown';
}
