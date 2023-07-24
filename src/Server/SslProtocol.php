<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

enum SslProtocol: string
{
    case TLS_1_1 = 'tls1.1';

    case TLS_1_2 = 'tls1.2';

    case TLS_1_3 = 'tls1.3';

    public function toSwooleInt(): int
    {
        return match ($this) {
            self::TLS_1_1 => SWOOLE_SSL_TLSv1,
            self::TLS_1_2 => SWOOLE_SSL_TLSv1_2,
            self::TLS_1_3 => SWOOLE_SSL_TLSv1_3,
        };
    }
}
