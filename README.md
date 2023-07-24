# Swoolefony

Swoolefony is a Swoole bundle for Symfony. It provides integration via a runtime class and management via commands.

# Installation

Install it using composer:

```bash
composer require swoolefony/swoole-bundle
```

# Runtime Configuration

Add the runtime class to your `composer.json` in the `extra` section:

```json
"extra": {
    "runtime": {
        "class": "Swoolefony\\SwooleBundle\\Runtime\\SwooleRuntime"
    }
}
```

With the runtime in place, the default `public/index.php` would be the entry point for start the application. Once it is run,
the Swoole server is started based on the provided configuration.

# Swoole Configuration

The Swoole server mode, IP, and port can be controlled via environment variables:

| Enviornment Variable              | Description                                                    | Default Value |
|-----------------------------------|----------------------------------------------------------------|---------------|
| `SWOOLEFONY_MODE`                 | The server mode to run Swoole in (e.g. `http`, `websocket`)    | `http`        |
| `SWOOLEFONY_IP`                   | The IP address to bind the Swoole server to.                   | `0.0.0.0`     |
| `SWOOLEFONY_PORT`                 | The port to for the Swoole server to listen on.                | `80`          |
| `SWOOLEFONY_SSL_CERT_FILE`        | The path to the SSL cert file to use.                          | Not set.      |
| `SWOOLEFONY_SSL_KEY_FILE`         | The path to the SSL key file to use.                           | Not set.      |
| `SWOOLEFONY_SSL_ALLOW_SELFSIGNED` | Whether or not to allow self-signed certificates (`0` or `1`). | `0`           |

The runtime class will look for those environment variables and use them if they exist. Otherwise it will fallback to the default
values that are listed.

# Request Attributes

The following attributes are available on the request class (`Request->attributes`) when it is processed by Symfony:

| Attribute Name    | Description                                                        | Value Type |
|-------------------|--------------------------------------------------------------------|------------|
| `swoolefony.id`   | The Swoole specific request ID (fd).                               | `int`      |
| `swoolefony.mode` | The server mode Swoole is running in. Either `http` or `websocket` | `string`   |

# Container Services

The following container services are available:

| Service Name        | Description                                                       | Class                                             |
|---------------------|-------------------------------------------------------------------|---------------------------------------------------|
| `swoolefony.server` | The Swoole server instance running for this request.              | `\Swoolefony\SwooleBundle\Server\ServerInterface` |
