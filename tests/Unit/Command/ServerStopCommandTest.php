<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Command;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Swoolefony\SwooleBundle\Command\ServerStopCommand;
use Swoolefony\SwooleBundle\Server\CacheKey;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Server\Status;
use Swoolefony\SwooleBundle\Swoole\ProcessTerminator;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(ServerStopCommand::class)]
class ServerStopCommandTest extends TestCase
{
    private CacheItemPoolInterface&MockInterface $mockCache;

    private InputInterface&MockInterface $mockInput;

    private OutputInterface&MockInterface $mockOutput;

    private ProcessTerminator&MockInterface $mockProcessStopper;

    private CacheItemInterface&MockInterface $mockCacheItem;

    private ServerStopCommand $subject;

    public function setUp(): void
    {
        parent::setUp();

        /** @var InputInterface&MockInterface $mockInput */
        $mockInput = Mockery::mock(InputInterface::class);
        /** @var OutputInterface&MockInterface $mockOutput */
        $mockOutput = Mockery::mock(OutputInterface::class);
        /** @var CacheItemPoolInterface&MockInterface $mockCache */
        $mockCache = Mockery::mock(CacheItemPoolInterface::class);
        /** @var ProcessTerminator&MockInterface $mockProcessStopper */
        $mockProcessStopper = Mockery::mock(ProcessTerminator::class);
        /** @var CacheItemInterface&MockInterface $mockCacheItem */
        $mockCacheItem = Mockery::mock(CacheItemInterface::class);

        $this->mockInput = $mockInput;
        $this->mockOutput = $mockOutput;
        $this->mockCache = $mockCache;
        $this->mockProcessStopper = $mockProcessStopper;
        $this->mockCacheItem = $mockCacheItem;

        $mockCache
            ->shouldReceive('getItem')
            ->andReturn($mockCacheItem);

        $mockOutput->allows(['writeln' => null]);
        $mockInput->allows(['get' => false]);

        $this->subject = new ServerStopCommand(
            cache: $this->mockCache,
            processTerminator: $this->mockProcessStopper,
        );
    }

    public function testItExitsWithFailureIfTheServerIsNotRunning(): void
    {
        $this->mockInput
            ->shouldReceive('getOption')
            ->with('force')
            ->andReturn(false);
        $this->mockCacheItem
            ->shouldReceive('isHit')
            ->andReturnFalse();

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
        $this->mockCacheItem
            ->shouldReceive('isHit')
            ->andReturnTrue();
        $this->mockCacheItem
            ->shouldReceive('get')
            ->andReturn(new Status(
                mainPid: 1,
                managerPid: 3,
                workerPid: 4,
                phpPid: 2,
                port: 808,
                ip: 'localhost',
                stats: new Stats([]),
            ));

        $this->mockProcessStopper
            ->shouldReceive('stop')
            ->andReturnTrue();

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
        $this->mockCacheItem
            ->shouldReceive('isHit')
            ->andReturnTrue();
        $this->mockCacheItem
            ->shouldReceive('get')
            ->andReturn(new Status(
                mainPid: 1,
                managerPid: 3,
                workerPid: 4,
                phpPid: 2,
                port: 808,
                ip: 'localhost',
                stats: new Stats([]),
            ));
        $this->mockCache
            ->shouldReceive('deleteItem');
        $this->mockProcessStopper
            ->shouldReceive('forceStop')
            ->andReturnTrue();

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
                'deleteItem',
                [CacheKey::ServerPid->value]
            );
    }
}
