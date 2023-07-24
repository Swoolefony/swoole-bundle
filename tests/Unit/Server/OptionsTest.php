<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Server;

use PHPUnit\Framework\Attributes\CoversClass;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Server\Options;
use Swoolefony\SwooleBundle\Server\SslProtocol;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(Options::class)]
class OptionsTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        self::resetEnv();
    }

    public function testItHasTheExpectedDefaults(): void
    {
        $subject = new Options();

        $this->assertSame(
            '0.0.0.0',
            $subject->getIpAddress(),
        );
        $this->assertSame(
            Mode::Http,
            $subject->getMode(),
        );
        $this->assertSame(
            80,
            $subject->getPort()
        );
    }

    public function testItSetsAndGetsThePort(): void
    {
        $subject = new Options(port: 8080);

        $this->assertSame(
            8080,
            $subject->getPort(),
        );
    }

    public function testItSetsAndGetsTheIp(): void
    {
        $subject = new Options(ip: '127.0.0.1');

        $this->assertSame(
            '127.0.0.1',
            $subject->getIpAddress(),
        );
    }

    public function testItSetsAndGetsTheMode(): void
    {
        $subject = new Options(mode: Mode::Websocket);

        $this->assertSame(
            Mode::Websocket,
            $subject->getMode(),
        );
    }

    public function testItSetsAndGetsTheSslCertFile(): void
    {
        $subject = new Options(sslCertFile: '/foo.crt');

        $this->assertSame(
            '/foo.crt',
            $subject->getSslCertFile(),
        );
    }

    public function testItSetsAndGetsTheSslKeyFile(): void
    {
        $subject = new Options(sslKeyFile: '/foo.key');

        $this->assertSame(
            '/foo.key',
            $subject->getSslKeyFile(),
        );
    }

    public function testItGetsTheDefaultSslProtocols(): void
    {
        $options = new Options();

        $this->assertSame(
            [SslProtocol::TLS_1_3, SslProtocol::TLS_1_2],
            $options->getSslProtocols(),
        );
    }

    public function testItMakesTheSwooleOptionsArray(): void
    {
        $options = new Options(
            sslCertFile: '/foo.crt',
            sslKeyFile: '/foo.key',
            allowSslSelfSigned: true,
        );

        $this->assertSame(
            [
                'hook_flags' => SWOOLE_HOOK_ALL,
                'ssl_key_file' => '/foo.key',
                'ssl_cert_file' => '/foo.crt',
                'ssl_allow_self_signed' => true,
                'ssl_protocols' => SWOOLE_SSL_TLSv1_3 | SWOOLE_SSL_TLSv1_2,
            ],
            $options->toSwooleOptionsArray()
        );
    }

    public function testItMakesTheOptionsFromEnv(): void
    {
        putenv('SWOOLEFONY_IP=192.168.1.1');
        putenv('SWOOLEFONY_PORT=8080');
        putenv('SWOOLEFONY_SSL_CERT_FILE=/foo.crt');
        putenv('SWOOLEFONY_SSL_KEY_FILE=/foo.key');
        putenv('SWOOLEFONY_SSL_ALLOW_SELFSIGNED=1');
        putenv('SWOOLEFONY_SSL_PROTOCOLS=tls1.3');

        $options = Options::makeFromArrayOrEnv();

        $this->assertSame(
            '192.168.1.1',
            $options->getIpAddress()
        );
        $this->assertSame(
            8080,
            $options->getPort()
        );
        $this->assertSame(
            '/foo.key',
            $options->getSslKeyFile(),
        );
        $this->assertSame(
            '/foo.crt',
            $options->getSslCertFile()
        );
        $this->assertTrue($options->isSslSelfSignedAllowed());
        $this->assertSame(
            [SslProtocol::TLS_1_3],
            $options->getSslProtocols()
        );
    }

    private static function resetEnv(): void
    {
        putenv('SWOOLEFONY_IP=');
        putenv('SWOOLEFONY_PORT=');
        putenv('SWOOLEFONY_SSL_CERT_FILE=');
        putenv('SWOOLEFONY_SSL_KEY_FILE=');
        putenv('SWOOLEFONY_SSL_ALLOW_SELFSIGNED=');
        putenv('SWOOLEFONY_SSL_PROTOCOLS=');
    }
}
