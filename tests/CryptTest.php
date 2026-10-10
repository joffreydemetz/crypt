<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Key;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    #[DataProviderExternal(SimpleCipherTest::class, 'knownAnswerProvider')]
    public function testEncryptAndDecryptMatchSimpleCipherKnownAnswers(string $key, string $plaintext, string $ciphertext): void
    {
        $crypt = new Crypt(new SimpleCipher(), new Key($key));

        $this->assertSame($ciphertext, $crypt->encrypt($plaintext));
        $this->assertSame($plaintext, $crypt->decrypt($ciphertext));
    }
}
