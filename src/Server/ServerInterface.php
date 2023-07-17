<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

interface ServerInterface
{
    public function run(): void;
}
