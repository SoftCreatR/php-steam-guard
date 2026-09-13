<?php

declare(strict_types=1);

namespace SoftCreatR\SteamGuard;

use InvalidArgumentException;
use SensitiveParameter;

use function base64_decode;
use function hash_hmac;
use function intdiv;
use function ord;
use function pack;
use function strlen;
use function time;

/**
 * Generates Steam Guard login codes from a mobile authenticator shared secret.
 */
final class CodeGenerator
{
    public const CODE_LENGTH = 5;
    public const PERIOD = 30;

    private const ALPHABET = '23456789BCDFGHJKMNPQRTVWXY';
    private const SHARED_SECRET_LENGTH = 20;

    /** @var non-empty-string */
    private readonly string $sharedSecret;

    /**
     * @param string $sharedSecret The base64-encoded `shared_secret` from a Steam mobile authenticator.
     */
    public function __construct(#[SensitiveParameter] string $sharedSecret)
    {
        $decodedSecret = base64_decode($sharedSecret, true);

        if ($decodedSecret === false || strlen($decodedSecret) !== self::SHARED_SECRET_LENGTH) {
            throw new InvalidArgumentException(
                'The shared secret must be valid base64 that decodes to exactly 20 bytes.',
            );
        }

        $this->sharedSecret = $decodedSecret;
    }

    /**
     * Generates the code for the supplied Unix timestamp, or for the current system time.
     */
    public function generateCode(?int $timestamp = null): string
    {
        $timestamp ??= time();

        if ($timestamp < 0) {
            throw new InvalidArgumentException('The timestamp must not be negative.');
        }

        // Steam signs the 30-second counter as an unsigned, big-endian 64-bit integer.
        $counter = intdiv($timestamp, self::PERIOD);
        $hash = hash_hmac('sha1', pack('J', $counter), $this->sharedSecret, true);
        $offset = ord($hash[19]) & 0x0f;
        $codePoint = ((ord($hash[$offset]) & 0x7f) << 24)
            | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8)
            | ord($hash[$offset + 3]);

        $code = '';
        $alphabetLength = strlen(self::ALPHABET);

        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= self::ALPHABET[$codePoint % $alphabetLength];
            $codePoint = intdiv($codePoint, $alphabetLength);
        }

        return $code;
    }

    /**
     * Prevent accidental disclosure through var_dump().
     *
     * @return array<never, never>
     */
    public function __debugInfo(): array
    {
        return [];
    }
}
