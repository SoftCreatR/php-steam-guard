# PHP Steam Guard

A small, dependency-free Steam Guard login-code generator for PHP 8.1 and newer.

CI covers PHP 8.1 through PHP 8.6; PHP 8.6 is tested against its current
pre-release builds until the stable release is available.

The generator is deliberately offline. Creating a code only needs a mobile
authenticator's base64-encoded `shared_secret` and the current Unix time; it
does not need an HTTP client or a request to Steam.

## Installation

```bash
composer require softcreatr/php-steam-guard
```

## Usage

```php
use SoftCreatR\SteamGuard\CodeGenerator;

$generator = new CodeGenerator($sharedSecret);
$code = $generator->generateCode();
```

Pass a Unix timestamp when deterministic generation is useful, for example in
a test:

```php
$code = $generator->generateCode(1_616_374_841);
```

Steam Guard changes its five-character code every 30 seconds. Keep the host's
system clock synchronized (normally with NTP). The library does not hide clock
or network failures by attempting its own time synchronization.

## Migrating from 0.0.1

Version 1.0 intentionally replaces the old API:

```php
// Before
$generator = new SoftCreatR\SteamGuard\CodeGen($sharedSecret);
$code = $generator->generateSteamGuardCode();

// Now
$generator = new SoftCreatR\SteamGuard\CodeGenerator($sharedSecret);
$code = $generator->generateCode();
```

- `CodeGen` was replaced by `CodeGenerator`.
- `generateSteamGuardCodeForTime($time)` became `generateCode($time)`.
- `TimeAligner` was removed. Code generation no longer performs network I/O.
- Guzzle and `ext-json` are no longer runtime dependencies.
- Invalid shared secrets are rejected immediately with an
  `InvalidArgumentException`.
- The supported PHP range is PHP 8.1 through PHP 8.6.

## Security

A `shared_secret` can generate valid login codes for its Steam account. Treat
it like a password: never commit it, log it, include it in an exception, or
send it to a third-party service. On PHP 8.2 and newer, the constructor argument
is marked sensitive for backtraces. The decoded secret is also kept in a private
readonly property and hidden from ordinary debug output, but callers remain
responsible for storing the original secret securely.

## License

ISC
