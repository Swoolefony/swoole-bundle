<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

use Co;
use Swoolefony\SwooleBundle\Server\Factory;
use Swoolefony\SwooleBundle\Server\Options;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class SwooleRuntime extends SymfonyRuntime
{
    private Options $swooleOptions;

    /**
     * @param array<string, scalar> $options
     */
    public function __construct(array $options = [])
    {
        $this->swooleOptions = Options::makeFromArrayOrEnv($options);

        parent::__construct($options);
    }

    /**
     * @inheritDoc
     */
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof Kernel) {
            $application->boot();

            /** @var Factory $factory */
            $factory = $application->getContainer()->get(Factory::class);

            return new ServerRunner(
                $this->swooleOptions,
                $application,
                $factory,
            );
        }
        Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

        return parent::getRunner($application);
    }
}
