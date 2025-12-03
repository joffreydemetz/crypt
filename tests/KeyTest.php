<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Key;
use PHPUnit\Framework\TestCase;

class KeyTest extends TestCase
{
    public function testConstructorWithPublicKeyOnly(): void
    {
        $publicKey = 'test-public-key';
        $key = new Key($publicKey);

        $this->assertSame($publicKey, $key->public);
        $this->assertSame($publicKey, $key->private);
    }

    public function testConstructorWithPublicAndPrivateKeys(): void
    {
        $publicKey = 'test-public-key';
        $privateKey = 'test-private-key';
        $key = new Key($publicKey, $privateKey);

        $this->assertSame($publicKey, $key->public);
        $this->assertSame($privateKey, $key->private);
    }

    public function testConstructorWithNullPrivateKey(): void
    {
        $publicKey = 'test-public-key';
        $key = new Key($publicKey, null);

        $this->assertSame($publicKey, $key->public);
        $this->assertSame($publicKey, $key->private);
    }

    public function testPublicAndPrivatePropertiesArePublic(): void
    {
        $key = new Key('test-key');

        // Test that properties can be accessed directly
        $this->assertIsString($key->public);
        $this->assertIsString($key->private);
    }
}
