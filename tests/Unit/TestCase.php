<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    use MockeryPHPUnitIntegration;
}
