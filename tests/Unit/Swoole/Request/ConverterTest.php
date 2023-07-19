<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Tests\Unit\Swoole\Request;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Swoole\Http\Request;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Swoolefony\SwooleBundle\Swoole\Request\Attribute;
use Swoolefony\SwooleBundle\Swoole\Request\Converter;
use Swoolefony\SwooleBundle\Tests\Unit\TestCase;

#[CoversClass(Converter::class)]
final class ConverterTest extends TestCase
{
    private Converter $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new Converter();
    }

    public function testItSetsAttributesForTheRequest(): void
    {
        $swooleRequest = self::makeMockSwooleRequest();

        $symfonyRequest = $this->subject->httpToSymfony(
            $swooleRequest,
            Mode::Http,
        );

        $this->assertSame(
            'http',
            $symfonyRequest->attributes->get(Attribute::Mode->value),
        );
        $this->assertSame(
            1,
            $symfonyRequest->attributes->get(Attribute::Id->value),
        );
    }

    public function testItSetsTheMethod(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(method: 'POST'),
            Mode::Http,
        );

        $this->assertSame(
            'POST',
            $symfonyRequest->getMethod(),
        );
    }

    public function testItSetsTheContent(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(content: 'content'),
            Mode::Http,
        );

        $this->assertSame(
            'content',
            $symfonyRequest->getContent(),
        );
    }

    public function testItSetsTheRequestUri(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(path: '/meh'),
            Mode::Http,
        );

        $this->assertSame(
            '/meh',
            $symfonyRequest->getRequestUri(),
        );
    }

    public function testItSetsTheCookieInfo(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(cookies: ['om' => ['nom', 'nom']]),
            Mode::Http,
        );

        $this->assertSame(
            ['om' => ['nom', 'nom']],
            $symfonyRequest->cookies->all(),
        );
    }

    public function testItSetsTheFilesInfo(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(
                files: [
                    'file1' => [
                        'name' => 'foo.bar',
                        'type' => 'text/plain'
                    ]
                ]
            ),
            Mode::Http,
        );

        $this->assertSame(
            [
                'file1' => [
                    'name' => 'foo.bar',
                    'type' => 'text/plain'
                ],
            ],
            $symfonyRequest->files->all(),
        );
    }

    public function testItSetsTheServerInfo(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(
                path: '/bar',
                server: [
                    'remote_addr' => '127.0.0.1',
                    'remote_port' => 80,
                ],
            ),
            Mode::Http,
        );

        $this->assertSame(
            '127.0.0.1',
            $symfonyRequest->server->get('REMOTE_ADDR'),
        );
        $this->assertSame(
            80,
            $symfonyRequest->server->get('REMOTE_PORT'),
        );
        $this->assertSame(
            '/bar',
            $symfonyRequest->server->get('REQUEST_URI'),
        );
    }

    public function testItSetsTheGetParams(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(get: ['foo' => 'meh', 'bar' => 'bleh']),
            Mode::Http,
        );

        $this->assertSame(
            ['foo' => 'meh', 'bar' => 'bleh'],
            $symfonyRequest->query->all(),
        );
    }

    public function testItSetsThePostParams(): void
    {
        $symfonyRequest = $this->subject->httpToSymfony(
            self::makeMockSwooleRequest(
                method: 'PATCH',
                post: ['foo' => 'meh', 'bar' => 'bleh']
            ),
            Mode::Http,
        );

        $this->assertSame(
            ['foo' => 'meh', 'bar' => 'bleh'],
            $symfonyRequest->request->all(),
        );
    }

    /**
     * @param array<string, string[]>|null $cookies
     * @param array<string, array<string, mixed>>|null $files
     * @param array<string, scalar>|null $server
     * @param array<string, scalar>|null $get
     * @param array<string, scalar>|null $post
     */
    private static function makeMockSwooleRequest(
        int $fd = 1,
        string $path = '/foo',
        string $content = 'bar',
        string $method = 'GET',
        ?array $cookies = null,
        ?array $files = null,
        ?array $server = null,
        ?array $get = null,
        ?array $post = null,
    ): Request&MockInterface {
        /** @var Request&MockInterface $swooleRequest */
        $swooleRequest = Mockery::mock(Request::class);
        $swooleRequest->fd = $fd;
        $swooleRequest->server = array_merge(
            ['request_uri' => $path],
            (array) $server,
        );
        $swooleRequest->cookie = $cookies;
        $swooleRequest->files = $files;
        $swooleRequest->get = $get;
        $swooleRequest->post = $post;

        $swooleRequest->allows([
            'getContent' => $content,
            'getMethod' => $method,
        ]);

        return $swooleRequest;
    }
}
