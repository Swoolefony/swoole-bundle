<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server;

use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Server\Stats;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(Stats::class)]
class StatsTest extends TestCase
{
    public function testGetStartTime(): void
    {
        $subject = new Stats(['start_time' => 10588280400]);

        $this->assertSame(
            10588280400,
            $subject->getStartTime()->getTimestamp()
        );
    }

    public function testGetAcceptedConnectionCount(): void
    {
        $subject = new Stats(['accept_count' => 2]);

        $this->assertSame(
            2,
            $subject->getAcceptedConnectionCount()
        );
    }

    public function testGetAbortedConnectionCount(): void
    {
        $subject = new Stats(['abort_count' => 2]);

        $this->assertSame(
            2,
            $subject->getAbortedConnectionCount()
        );
    }

    public function testGetClosedConnectionCount(): void
    {
        $subject = new Stats(['close_count' => 3]);

        $this->assertSame(
            3,
            $subject->getClosedConnectionCount()
        );
    }

    public function testGetNumberOfWorkers(): void
    {
        $subject = new Stats(['worker_num' => 1]);

        $this->assertSame(
            1,
            $subject->getNumberOfWorkers()
        );
    }

    public function testGetNumberOfTaskWorkers(): void
    {
        $subject = new Stats(['task_worker_num' => 1]);

        $this->assertSame(
            1,
            $subject->getNumberOfTaskWorkers()
        );
    }

    public function testGetNumberOfUserWorkers(): void
    {
        $subject = new Stats(['user_worker_num' => 1]);

        $this->assertSame(
            1,
            $subject->getNumberOfUserWorkers()
        );
    }

    public function testGetNumberOfIdleWorkers(): void
    {
        $subject = new Stats(['idle_worker_num' => 1]);

        $this->assertSame(
            1,
            $subject->getNumberOfIdleWorkers()
        );
    }

    public function testGetRequestCount(): void
    {
        $subject = new Stats(['request_count' => 5]);

        $this->assertSame(
            5,
            $subject->getRequestCount()
        );
    }

    public function testGetResponseCount(): void
    {
        $subject = new Stats(['response_count' => 5]);

        $this->assertSame(
            5,
            $subject->getResponseCount()
        );
    }

    public function testGetDispatchCount(): void
    {
        $subject = new Stats(['dispatch_count' => 5]);

        $this->assertSame(
            5,
            $subject->getDispatchCount()
        );
    }

    public function testGetTotalBytesSent(): void
    {
        $subject = new Stats(['total_recv_bytes' => 9001]);

        $this->assertSame(
            9001,
            $subject->getTotalBytesReceived()
        );
    }

    public function testGetTotalBytesReceived(): void
    {
        $subject = new Stats(['total_send_bytes' => 9001]);

        $this->assertSame(
            9001,
            $subject->getTotalBytesSent()
        );
    }

    public function testToArray(): void
    {
        $stats = [
            'start_time' => 10588280400,
            'accept_count' => 1,
            'abort_count' => 2,
            'close_count' => 3,
            'worker_num' => 4,
            'task_worker_num' => 5,
            'user_worker_num' => 6,
            'idle_worker_num' => 7,
            'request_count' => 8,
            'response_count' => 9,
            'dispatch_count' => 10,
            'total_recv_bytes' => 11,
            'total_send_bytes' => 12,
        ];

        $subject = new Stats($stats);

        $result = $subject->toArray();

        $stats['start_time'] = (new \DateTimeImmutable('@' . $stats['start_time']));

        // Intentionally truthy check because of the DateTime.
        $this->assertEquals(
            $stats,
            $result,
        );
    }
}
