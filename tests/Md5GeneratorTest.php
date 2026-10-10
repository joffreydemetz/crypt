<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Md5Generator;
use PHPUnit\Framework\TestCase;

class Md5GeneratorTest extends TestCase
{
    public function testKeyIsTheMd5OfTheSaltDrawnString(): void
    {
        $generator = new Md5Generator();
        $generator->setSalt('x');
        $generator->setLength(5);

        $key = $generator->generateKey();

        // md5('xxxxx'): the length applies to the hashed string, not to the key
        $this->assertSame('fb0e22c79ac75679e9881e6ba183b354', $key->public);
        $this->assertSame($key->public, $key->private);
    }

    public function testDefaultKeysAreRandom32CharacterLowercaseHex(): void
    {
        $generator = new Md5Generator();

        $first = $generator->generateKey()->public;
        $second = $generator->generateKey()->public;

        $this->assertMatchesRegularExpression('/\A[0-9a-f]{32}\z/', $first);
        $this->assertNotSame($first, $second);
    }
}
