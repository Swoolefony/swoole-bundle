<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use RuntimeException;
use Swoole\Http\Server as SwooleServer;

class Server implements ServerInterface
{
    private const HANDLED_EVENTS = [
        EventName::Request,
        EventName::Start,
        EventName::Shutdown,
        EventName::Task,
    ];

    private int $pid;

    private ?SwooleServer $swooleServer = null;

    public function __construct(
        private readonly Options $options,
        private readonly HandlerFactory $handlerFactory,
        private readonly object $app,
    ) {
    }

    public function run(): void
    {
        $pid = getmypid();
        if ($pid === false) {
            throw new RuntimeException('Unable to get the current process pid.');
        }
        $this->pid = $pid;

        if (!$this->server()->start()) {
            throw new RuntimeException('Unable to start HTTP server.');
        }
    }

    public function stop(): void
    {
        if ($this->swooleServer) {
            $this->server()->shutdown();
        }
    }

    public function getStatus(): Status
    {
        $mainPid = $this->server()->getMasterPid();
        $managerPid = $this->server()->getManagerPid();
        $workerPid = $this->server()->getWorkerPid();

        if (!is_int($mainPid)) {
            throw new \UnexpectedValueException('Unable to get the main pid.');
        }
        if (!is_int($managerPid)) {
            throw new \UnexpectedValueException('Unable to get the manager pid.');
        }
        if (!is_int($workerPid)) {
            throw new \UnexpectedValueException('Unable to get the worker pid.');
        }

        return new Status(
            mainPid: $mainPid,
            managerPid: $managerPid,
            workerPid: $workerPid,
            phpPid: $this->pid,
            port: $this->server()->port,
            ip: $this->server()->host,
            stats: new Stats((array) $this->server()->stats())
        );
    }

    private function server(): SwooleServer
    {
        if ($this->swooleServer !== null) {
            return $this->swooleServer;
        }
        $this->swooleServer = new SwooleServer(
            $this->options->getIpAddress(),
            $this->options->getPort(),
        );

        $this->swooleServer->set($this->options->toSwooleOptionsArray());

        foreach (self::HANDLED_EVENTS as $eventName) {
            $handler = $this->handlerFactory->makeForEvent(
                $eventName,
                $this->app,
            );
            if ($handler !== null) {
                $this->swooleServer->on(
                    $eventName->value,
                    $handler,
                );
            }
        }

        return $this->swooleServer;
    }
}
