<?php

declare(strict_types=1);

namespace Swoolefony\SwooleBundle\Server;

use Swoolefony\SwooleBundle\Runtime\Mode;

class Options
{
    public function __construct(
        private string $ip = '0.0.0.0',
        private int $port = 80,
        private Mode $mode = Mode::Http,
        private ?string $sslCertFile = null,
        private ?string $sslKeyFile = null,
        private bool $allowSslSelfSigned = false,
    ) {
    }

    /**
     * @param array<string, scalar> $options
     */
    public static function makeFromArrayOrEnv(array $options = []): self
    {
        $ip = $options['swoolefony_ip'] ?? getenv('SWOOLEFONY_IP');
        $port = $options['swoolefony_port'] ?? getenv('SWOOLEFONY_PORT');
        $mode = $options['swoolefony_mode'] ?? getenv('SWOOLEFONY_MODE');
        $sslCertFile = $options['swoolefony_ssl_cert_file'] ?? getenv('SWOOLEFONY_SSL_CERT_FILE');
        $sslKeyFile = $options['swoolefony_ssl_key_file'] ?? getenv('SWOOLEFONY_SSL_KEY_FILE');
        $isSslSelfSignedAllowed = $options['swoolefony_ssl_allow_selfsigned'] ?? getenv('SWOOLEFONY_SSL_ALLOW_SELFSIGNED');

        $optionsObj = new self();
        if (is_string($ip) && $ip !== '') {
            $optionsObj->setIpAddress($ip);
        }
        if ((is_string($port) && $port !== '') || is_int($port)) {
            $optionsObj->setPort((int) $port);
        }
        if (is_string($mode) && Mode::tryFrom($mode)) {
            $optionsObj->setMode(Mode::from($mode));
        }
        if (is_string($sslKeyFile) && $sslKeyFile !== '') {
            $optionsObj->setSslKeyFile($sslKeyFile);
        }
        if (is_string($sslCertFile) && $sslCertFile !== '') {
            $optionsObj->setSslCertFile($sslCertFile);
        }
        $optionsObj->setIsSslSelfSignedAllowed((bool) $isSslSelfSignedAllowed);

        return $optionsObj;
    }

    public function setIpAddress(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ip;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function setMode(Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getMode(): Mode
    {
        return $this->mode;
    }

    public function getSslCertFile(): ?string
    {
        return $this->sslCertFile;
    }

    public function setSslCertFile(?string $sslCertFile): self
    {
        $this->sslCertFile = $sslCertFile;

        return $this;
    }

    public function getSslKeyFile(): ?string
    {
        return $this->sslKeyFile;
    }

    public function setSslKeyFile(?string $sslKeyFile): self
    {
        $this->sslKeyFile = $sslKeyFile;

        return $this;
    }

    public function setIsSslSelfSignedAllowed(bool $allowSslSelfSigned): self
    {
        $this->allowSslSelfSigned = $allowSslSelfSigned;

        return $this;
    }

    public function isSslSelfSignedAllowed(): bool
    {
        return $this->allowSslSelfSigned;
    }

    /**
     * @return array<string, scalar>
     */
    public function toSwooleOptionsArray(): array
    {
        $options = [
            'hook_flags' => SWOOLE_HOOK_ALL,
        ];

        $sslKeyFile = $this->getSslKeyFile();
        $sslCertFile = $this->getSslCertFile();

        if ($sslKeyFile !== null) {
            $options['ssl_key_file'] = $sslKeyFile;
        }
        if ($sslCertFile !== null) {
            $options['ssl_cert_file'] = $sslCertFile;
        }
        if($sslKeyFile || $sslCertFile) {
            $options['ssl_allow_self_signed'] = $this->isSslSelfSignedAllowed();
        }

        return $options;
    }
}
