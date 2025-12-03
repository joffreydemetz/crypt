<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Key;
use PHPUnit\Framework\TestCase;

class SimpleCipherTest extends TestCase
{
    private SimpleCipher $cipher;

    protected function setUp(): void
    {
        $this->cipher = new SimpleCipher();
    }

    public function testEncryptDecryptWithSameKey(): void
    {
        $originalText = 'Hello World!';
        $key = new Key('secret-key-123');

        $encrypted = $this->cipher->encrypt($originalText, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertNotEquals($originalText, $encrypted);
        $this->assertSame($originalText, $decrypted);
    }

    public function testEncryptProducesHexString(): void
    {
        $originalText = 'Test';
        $key = new Key('test-key');

        $encrypted = $this->cipher->encrypt($originalText, $key);

        // Verify the result is hexadecimal (contains only 0-9, A-F, and spaces)
        $this->assertMatchesRegularExpression('/^[0-9A-F ]+$/', $encrypted);
    }

    public function testEncryptDecryptEmptyString(): void
    {
        $originalText = '';
        $key = new Key('key');

        $encrypted = $this->cipher->encrypt($originalText, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertSame($originalText, $decrypted);
    }

    public function testEncryptDecryptLongText(): void
    {
        $originalText = str_repeat('This is a longer text that repeats. ', 10);
        $key = new Key('my-secret-key');

        $encrypted = $this->cipher->encrypt($originalText, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertSame($originalText, $decrypted);
    }

    public function testEncryptDecryptSpecialCharacters(): void
    {
        $originalText = "Special chars: !@#$%^&*()_+-=[]{}|;':\",./<>?";
        $key = new Key('special-key');

        $encrypted = $this->cipher->encrypt($originalText, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertSame($originalText, $decrypted);
    }

    public function testEncryptDecryptUnicodeCharacters(): void
    {
        $originalText = 'Unicode: こんにちは 世界 🌍';
        $key = new Key('unicode-key');

        $encrypted = $this->cipher->encrypt($originalText, $key);
        $decrypted = $this->cipher->decrypt($encrypted, $key);

        $this->assertSame($originalText, $decrypted);
    }

    public function testDifferentKeysProduceDifferentResults(): void
    {
        $originalText = 'Same text';
        $key1 = new Key('key-one');
        $key2 = new Key('key-two');

        $encrypted1 = $this->cipher->encrypt($originalText, $key1);
        $encrypted2 = $this->cipher->encrypt($originalText, $key2);

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testDecryptWithWrongKeyProducesGarbage(): void
    {
        $originalText = 'Secret Message';
        $correctKey = new Key('correct-key');
        $wrongKey = new Key('wrong-key');

        $encrypted = $this->cipher->encrypt($originalText, $correctKey);
        $decryptedWithWrongKey = $this->cipher->decrypt($encrypted, $wrongKey);

        $this->assertNotEquals($originalText, $decryptedWithWrongKey);
    }
}
