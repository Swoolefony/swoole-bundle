<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

enum Mode: string
{
    case Http = 'http';

    case Websocket = 'websocket';
}
