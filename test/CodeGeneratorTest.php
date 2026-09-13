<?php

declare(strict_types=1);

namespace SoftCreatR\SteamGuard\Test;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SoftCreatR\SteamGuard\CodeGenerator;

#[CoversClass(CodeGenerator::class)]
final class CodeGeneratorTest extends TestCase
{
    private const SHARED_SECRET = 'zvIayp3JPvtvX/QGHqsqKBk/44s=';

    /**
     * @see https://github.com/dyc3/steamguard-cli/blob/master/steamguard/src/token.rs
     */
    public function testMatchesIndependentSteamGuardVector(): void
    {
        $generator = new CodeGenerator(self::SHARED_SECRET);

        self::assertSame('2F9J5', $generator->generateCode(1_616_374_841));
    }

    public function testCodeIsStableWithinThirtySecondWindow(): void
    {
        $generator = new CodeGenerator(self::SHARED_SECRET);

        self::assertSame(
            $generator->generateCode(1_616_374_830),
            $generator->generateCode(1_616_374_859),
        );
        self::assertNotSame(
            $generator->generateCode(1_616_374_859),
            $generator->generateCode(1_616_374_860),
        );
    }

    public function testGeneratesCodeForCurrentSystemTime(): void
    {
        $generator = new CodeGenerator(self::SHARED_SECRET);

        self::assertMatchesRegularExpression(
            '/^[23456789BCDFGHJKMNPQRTVWXY]{5}$/',
            $generator->generateCode(),
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidSecrets(): iterable
    {
        yield 'empty' => [''];
        yield 'not base64' => ['not base64!'];
        yield 'wrong decoded length' => ['VGVzdA=='];
    }

    #[DataProvider('invalidSecrets')]
    public function testRejectsInvalidSharedSecret(string $sharedSecret): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('valid base64');

        new CodeGenerator($sharedSecret);
    }

    public function testRejectsNegativeTimestamp(): void
    {
        $generator = new CodeGenerator(self::SHARED_SECRET);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('timestamp');

        $generator->generateCode(-1);
    }

    public function testHidesSecretFromDebugOutput(): void
    {
        $generator = new CodeGenerator(self::SHARED_SECRET);

        self::assertSame([], $generator->__debugInfo());
    }
}
