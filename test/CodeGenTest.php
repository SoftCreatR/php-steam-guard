<?php

namespace SoftCreatR\SteamGuard\Test;

use RuntimeException;
use SoftCreatR\SteamGuard\CodeGen;

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
        $this->expectError();

        $codeGen = new CodeGen(null);
        $codeGen->generateSteamGuardCode();
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
