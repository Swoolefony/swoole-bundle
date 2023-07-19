<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Swoole\Request;

use Swoole\Http\Request as SwooleRequest;
use Swoolefony\SwooleBundle\Runtime\Mode;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use function array_change_key_case;
use function array_merge;
use function is_string;

class Converter
{
    /**
     * Given a Swoole HTTP request, convert it to a Symfony request object.
     */
    public function httpToSymfony(
        SwooleRequest $request,
        Mode $mode,
    ): SymfonyRequest {
        $content = $request->getContent();
        $method = $request->getMethod();

        $symfonyRequest = SymfonyRequest::create(
            $request->server['request_uri'],
            is_string($method) ? $method : 'GET',
            array_merge(
                (array) $request->get,
                (array) $request->post,
            ),
            (array) $request->cookie,
            (array) $request->files,
            array_change_key_case((array) $request->server, CASE_UPPER),
            $content === false ? null : $content,
        );

        $symfonyRequest->attributes->add([
            Attribute::Id->value => $request->fd,
            Attribute::Mode->value => $mode->value,
        ]);

        return $symfonyRequest;
    }
}
