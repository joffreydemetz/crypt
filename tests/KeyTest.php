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
}
