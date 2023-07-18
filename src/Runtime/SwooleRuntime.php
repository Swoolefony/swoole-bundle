<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

use Co;
use Swoolefony\SwooleBundle\Server\Options;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class SwooleRuntime extends SymfonyRuntime
{
    private int $port = 80;

    private string $ip = '0.0.0.0';

    private Mode $mode = Mode::Http;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->parseOptions($options);

        parent::__construct($options);
    }

    /**
     * @inheritDoc
     */
    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof HttpKernelInterface) {
            return $this->makeServerRunner($application);
        }
        Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

        return parent::getRunner($application);
    }

    private function makeServerRunner(HttpKernelInterface $httpKernel): ServerRunner
    {
        $options = new Options(
            $this->ip,
            $this->port,
            $this->mode,
        );

        return new ServerRunner(
            $options,
            $httpKernel,
        );
    }

    /**
     * @param array<string, mixed> $options
     */
    private function parseOptions(array $options): void
    {
        $ip = $options['swoolefony_ip'] ?? getenv('SWOOLEFONY_IP');
        $port = $options['swoolefony_port'] ?? getenv('SWOOLEFONY_PORT');
        $mode = $options['swoolefony_mode'] ?? getenv('SWOOLEFONY_MODE');

        if (is_string($ip)) {
            $this->ip = $ip;
        }
        if (is_string($port) || is_int($port)) {
            $this->port = (int) $port;
        }
        if (is_string($mode) && Mode::tryFrom($mode)) {
            $this->mode = Mode::from($mode);
        }
    }
}
