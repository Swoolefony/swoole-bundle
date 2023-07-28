<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Command;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Process;
use Swoolefony\SwooleBundle\Command\ServerStopCommand;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[CoversClass(ServerStopCommand::class)]
class ServerStopCommandTest extends TestCase
{
    private CacheInterface&MockInterface $mockCache;

    private InputInterface&MockInterface $mockInput;

    private OutputInterface&MockInterface $mockOutput;

    private ServerStopCommand $subject;

    private Process $testProcess;

    public function setUp(): void
    {
        parent::setUp();

        /** @var InputInterface&MockInterface $mockInput */
        $mockInput = Mockery::mock(InputInterface::class);
        /** @var OutputInterface&MockInterface $mockOutput */
        $mockOutput = Mockery::mock(OutputInterface::class);
        /** @var CacheInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheInterface::class);

        $this->mockInput = $mockInput;
        $this->mockOutput = $mockOutput;
        $this->mockCache = $mockCache;

        $mockOutput->allows(['writeln' => null]);
        $mockInput->allows(['get' => false]);

        $this->subject = new ServerStopCommand($this->mockCache);

        $this->testProcess = new Process(function () {
            // @phpstan-ignore-next-line
            while (true) {
                sleep(1);
            }
        });
        $this->testProcess->setBlocking(false);
        $this->testProcess->start();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->testProcess->exit();
    }

    public function testItExitsWithFailureIfTheServerIsNotRunning(): void
    {
        $this->mockInput
            ->shouldReceive('getOption')
            ->with('force')
            ->andReturn(false);
        $this->mockCache
            ->shouldReceive('get')
            ->andReturn(null);

        $result = $this->subject->execute(
            $this->mockInput,
            $this->mockOutput,
        );

        $this->assertSame(
            Command::FAILURE,
            $result
        );
    }

    public function testItStopsTheRunningProcess(): void
    {
        $this->mockInput
            ->shouldReceive('getOption')
            ->with('force')
            ->andReturn(false);
        $this->mockCache
            ->shouldReceive('get')
            ->andReturn($this->testProcess->pid);

        $result = $this->subject->execute(
            $this->mockInput,
            $this->mockOutput,
        );

        $this->assertSame(
            Command::SUCCESS,
            $result
        );
        $this->mockCache
            ->shouldNotHaveReceived('delete');
    }

    public function testItForceStopsTheRunningProcessIfSpecified(): void
    {
        $this->mockInput
            ->shouldReceive('getOption')
            ->with('force')
            ->andReturn(true);
        $this->mockCache
            ->shouldReceive('get')
            ->andReturn($this->testProcess->pid);
        $this->mockCache
            ->shouldReceive('delete');

        $result = $this->subject->execute(
            $this->mockInput,
            $this->mockOutput,
        );

        $this->assertSame(
            Command::SUCCESS,
            $result
        );
        $this->mockCache
            ->shouldHaveReceived(
                'delete',
                [CacheKey::ServerPid->value]
            );
    }
}
