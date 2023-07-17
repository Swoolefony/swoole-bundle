<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Runtime;

use Swoolefony\SwooleBundle\Runtime\Runner\HttpRunner;
use Swoolefony\SwooleBundle\Server\Http\Handler\RequestHandler;
use Swoolefony\SwooleBundle\Server\Http\HttpServerFactory;
use Swoolefony\SwooleBundle\Server\Http\Options;
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
            return $this->makeHttpRunner($application);
        }

        return parent::getRunner($application);
    }

    private function makeHttpRunner(HttpKernelInterface $httpKernel): HttpRunner
    {
        $options = new Options(
            $this->ip,
            $this->port,
        );
        $options->setRequestHandler(
            (new RequestHandler(
                $httpKernel,
                $this->mode
            ))(...)
        );

        return match ($this->mode) {
            Mode::Http => new HttpRunner(
                new HttpServerFactory(),
                $options,
                $httpKernel,
            ),
            default => throw new \RuntimeException(sprintf(
                'The mode with name %s was not expected.',
                $this->mode->name,
            ))
        };
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
