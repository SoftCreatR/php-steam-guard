<?php

namespace SoftCreatR\SteamGuard;

use RuntimeException;

use function base64_decode;
use function floor;
use function hash_hmac;
use function pack;
use function strlen;
use function unpack;

/**
 * This library is heavily inspired by
 *
 * @link https://github.com/MarlonColhado/SteamGenerateMobileAuthPHP
 * @link https://github.com/geel9/SteamAuth/blob/master/SteamAuth
 * @link https://gist.github.com/mooop12/1af7f0ffc8f28ea76f27abcba1e6da01
 * @link https://github.com/SteamTimeIdler/stidler/wiki/Getting-your-%27shared_secret%27-code-for-use-with-Auto-Restarter-on-Mobile-Authentication
 */
class CodeGen
{
    private static string $steamChars = '23456789BCDFGHJKMNPQRTVWXY';
    private string $sharedSecret;

    public function __construct(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function generateSteamGuardCode(): string
    {
        return $this->generateSteamGuardCodeForTime((new TimeAligner())->getSteamTime());
    }

    /**
     * @throws RuntimeException
     */
    public function generateSteamGuardCodeForTime(int $time): string
    {
        if (empty($this->sharedSecret)) {
            throw new RuntimeException('No shared secret provided.');
        }

        $sharedSecret = base64_decode($this->sharedSecret);
        $timeHash = pack('N*', 0) . pack('N*', floor($time / 30));
        $hmac = unpack('C*', pack('H*', hash_hmac('sha1', $timeHash, $sharedSecret, false)));
        $hashedData = [];
        $modeIndex = 0;

        foreach ($hmac as $value) {
            $hashedData[$modeIndex] = $this->intToByte($value);
            $modeIndex++;
        }

        $b = $this->intToByte(($hashedData[19] & 0xF));
        $charLen = strlen(self::$steamChars);
        $codePoint =
            ($hashedData[$b] & 0x7F) << 24 |
            ($hashedData[$b + 1] & 0xFF) << 16 |
            ($hashedData[$b + 2] & 0xFF) << 8 |
            ($hashedData[$b + 3] & 0xFF);

        $code = '';

        for ($i = 0; $i < 5; ++$i) {
            $code .= self::$steamChars[$codePoint % $charLen];
            $codePoint /= $charLen;
        }

        return $code;
    }

    private function intToByte($int): int
    {
        return $int & (0xff);
    }
}
