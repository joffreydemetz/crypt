<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\SimpleGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SimpleGeneratorTest extends TestCase
{
    public function testDefaultKeyIs256AsciiAlphanumericCharactersOnBothHalves(): void
    {
        $key = (new SimpleGenerator())->generateKey();

        $this->assertMatchesRegularExpression('/\A[a-zA-Z0-9]{256}\z/', $key->public);
        $this->assertSame($key->public, $key->private);
    }

    public function testTwoDefaultKeysDiffer(): void
    {
        $generator = new SimpleGenerator();

        $this->assertNotSame($generator->generateKey()->public, $generator->generateKey()->public);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function lengthProvider(): iterable
    {
        yield 'one character' => [1];
        yield 'shorter than the default' => [17];
        yield 'longer than the default' => [1000];
    }

    #[DataProvider('lengthProvider')]
    public function testSingleCharacterSaltGivesADeterministicKeyOfTheSetLength(int $length): void
    {
        $generator = new SimpleGenerator();
        $generator->setSalt('x');
        $generator->setLength($length);

        $this->assertSame(str_repeat('x', $length), $generator->generateKey()->public);
    }

    public function testKeyDrawsFromEveryCharacterOfTheSaltAndNothingElse(): void
    {
        $generator = new SimpleGenerator();
        $generator->setSalt('ab');
        $generator->setLength(500);

        $public = $generator->generateKey()->public;

        $this->assertMatchesRegularExpression('/\A[ab]{500}\z/', $public);
        // both ends of the salt are drawn (a one-letter key has a 2^-499 chance)
        $this->assertStringContainsString('a', $public);
        $this->assertStringContainsString('b', $public);
    }

    public function testEmptySaltCannotGenerateAKey(): void
    {
        $generator = new SimpleGenerator();
        $generator->setSalt('');

        $this->expectException(\ValueError::class);

        $generator->generateKey();
    }


}
