<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

enum CacheKey: string
{
    case ServerPid = 'swoolefony.server.pid';

    case ServerStatus = 'swoolefony.server.status';
}
