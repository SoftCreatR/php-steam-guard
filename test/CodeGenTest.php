<?php

namespace SoftCreatR\SteamGuard\Test;

use Exception;
use RuntimeException;
use SoftCreatR\SteamGuard\CodeGen;
use TypeError;

/**
 * This class tests the CodeGen class.
 *
 * @coversDefaultClass \SoftCreatR\SteamGuard\CodeGen
 */
class CodeGenTest extends TestCase
{
    public function testGenerateSteamGuardCode(): void
    {
        $codeGen = new CodeGen('Test');
        $code = $codeGen->generateSteamGuardCode();

        $this->assertEquals(5, strlen($code));
    }

    public function testGenerateSteamGuardCodeThrowsTypeError(): void
    {
        // @see https://github.com/sebastianbergmann/phpunit/issues/5062#issuecomment-1416362657
        set_error_handler(static function (int $errno, string $errstr): void {
            throw new Exception($errstr, $errno);
        }, E_USER_WARNING);

        $this->expectException(TypeError::class);

        $codeGen = new CodeGen(null);
        $codeGen->generateSteamGuardCode();

        restore_error_handler();
    }

    public function testGenerateSteamGuardCodeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $codeGen = new CodeGen('');
        $codeGen->generateSteamGuardCode();
    }

    public function testGenerateSteamGuardCodeForTime(): void
    {
        $codeGen = new CodeGen('Test');
        $code = $codeGen->generateSteamGuardCodeForTime(1655007471);

        $this->assertEquals('M9YT8', $code);
    }
}
