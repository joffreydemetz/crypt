<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Sha1Generator;
use PHPUnit\Framework\TestCase;

class Sha1GeneratorTest extends TestCase
{
    public function testKeyIsTheSha1OfTheSaltDrawnString(): void
    {
        $generator = new Sha1Generator();
        $generator->setSalt('x');
        $generator->setLength(5);

        $key = $generator->generateKey();

        // sha1('xxxxx'): the length applies to the hashed string, not to the key
        $this->assertSame('9addbf544119efa4a64223b649750a510f0d463f', $key->public);
        $this->assertSame($key->public, $key->private);
    }

    public function testDefaultKeysAreRandom40CharacterLowercaseHex(): void
    {
        $generator = new Sha1Generator();

        $first = $generator->generateKey()->public;
        $second = $generator->generateKey()->public;

        $this->assertMatchesRegularExpression('/\A[0-9a-f]{40}\z/', $first);
        $this->assertNotSame($first, $second);
    }
}
