<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Key;
use JDZ\Crypt\Md5Cipher;
use JDZ\Crypt\Sha1Cipher;
use JDZ\Crypt\SimpleCipher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SimpleCipherTest extends TestCase
{
    private SimpleCipher $cipher;

    protected function setUp(): void
    {
        $this->cipher = new SimpleCipher();
    }

    /**
     * Ciphertexts produced by the current code, hardcoded so the format can never
     * change silently (repeating-key XOR, two uppercase hex characters per byte,
     * ' ' instead of '0' for a zero high nibble): stored ciphertext must stay
     * decryptable.
     *
     * @return iterable<string, array{string, string, string}> key, plaintext, ciphertext
     */
    public static function knownAnswerProvider(): iterable
    {
        yield 'plaintext equal to key: zero bytes, space padding' => ['key', 'key', ' 0 0 0'];
        yield 'ASCII sentence, key shorter than plaintext' => ['secret', 'Hello World!', '3B 0 F1E A5424 A111E 155'];
        yield 'key repeated cyclically' => ['abc', 'abcdefghij', ' 0 0 0 5 7 5 6 A A B'];
        yield 'key longer than plaintext: only its prefix is used' => ['longer-key', 'ab', ' D D'];
        yield 'NUL key: identity XOR, raw hex format' => ["\x00", "\x00\x0F\x10\xFF", ' 0 F10FF'];
        yield 'multibyte UTF-8 key and plaintext, XORed byte by byte' => ["cl\u{e9}", "\u{e9}t\u{e9}", 'A0C5B76ACA'];
        yield 'empty plaintext' => ['k', '', ''];
    }

    #[DataProvider('knownAnswerProvider')]
    public function testEncryptMatchesKnownAnswer(string $key, string $plaintext, string $ciphertext): void
    {
        $this->assertSame($ciphertext, $this->cipher->encrypt($plaintext, new Key($key)));
    }

    #[DataProvider('knownAnswerProvider')]
    public function testDecryptMatchesKnownAnswer(string $key, string $plaintext, string $ciphertext): void
    {
        $this->assertSame($plaintext, $this->cipher->decrypt($ciphertext, new Key($key)));
    }

    /**
     * @return iterable<string, array{string, string}> key, plaintext
     */
    public static function roundTripProvider(): iterable
    {
        yield 'ASCII sentence' => ['secret-key-123', 'Hello World!'];
        yield 'empty string' => ['key', ''];
        yield 'long text, key much shorter' => ['my-secret-key', str_repeat('This is a longer text that repeats. ', 10)];
        yield 'special characters' => ['special-key', "Special chars: !@#\$%^&*()_+-=[]{}|;':\",./<>?"];
        yield 'real newlines, CRLF and tabs' => ['newline-key', "Line1\nLine2\r\nLine3\tTabbed\n"];
        yield 'multibyte UTF-8' => ['unicode-key', "Unicode: \u{3053}\u{3093}\u{306B}\u{3061}\u{306F} \u{4E16}\u{754C} \u{1F30D} \u{e9}\u{e0}\u{e7}"];
        yield 'every byte 0x00-0xFF' => ['binary-key', implode('', array_map('chr', range(0, 255)))];
        yield 'binary key' => ["\x00\xFF\x80\x7F", 'ASCII text under a binary key'];
        yield 'multibyte key' => ["cl\u{e9}-\u{fc}-\u{1F511}", 'ASCII text under a multibyte key'];
    }

    #[DataProvider('roundTripProvider')]
    public function testDecryptRestoresWhatEncryptProduced(string $key, string $plaintext): void
    {
        $encrypted = $this->cipher->encrypt($plaintext, new Key($key));

        $this->assertSame(2 * strlen($plaintext), strlen($encrypted));
        $this->assertSame($plaintext, $this->cipher->decrypt($encrypted, new Key($key)));
    }

    public function testEncryptUsesThePrivateKeyAndDecryptThePublicKey(): void
    {
        // '3B 0 F1E A5424 A111E 155' is the known answer for key 'secret'
        $this->assertSame('3B 0 F1E A5424 A111E 155', $this->cipher->encrypt('Hello World!', new Key('ignored-public', 'secret')));
        $this->assertSame('Hello World!', $this->cipher->decrypt('3B 0 F1E A5424 A111E 155', new Key('secret', 'ignored-private')));
    }

    public function testDecryptAcceptsZeroAsWellAsSpaceForAZeroHighNibble(): void
    {
        // the ' 0 F10FF' known answer, written as conventional zero-padded hex
        $this->assertSame("\x00\x0F\x10\xFF", $this->cipher->decrypt('000F10FF', new Key("\x00")));
    }

    /**
     * Md5Cipher and Sha1Cipher are empty subclasses: no hashing happens.
     *
     * @return iterable<string, array{class-string<SimpleCipher>, string, string, string}>
     */
    public static function hashNamedCipherKnownAnswerProvider(): iterable
    {
        foreach (['Md5Cipher' => Md5Cipher::class, 'Sha1Cipher' => Sha1Cipher::class] as $name => $class) {
            foreach (self::knownAnswerProvider() as $row => $args) {
                yield "$name, $row" => [$class, ...$args];
            }
        }
    }

    /**
     * @param class-string<SimpleCipher> $class
     */
    #[DataProvider('hashNamedCipherKnownAnswerProvider')]
    public function testMd5AndSha1CiphersBehaveExactlyLikeSimpleCipher(string $class, string $key, string $plaintext, string $ciphertext): void
    {
        $cipher = new $class();

        $this->assertSame($ciphertext, $cipher->encrypt($plaintext, new Key($key)));
        $this->assertSame($plaintext, $cipher->decrypt($ciphertext, new Key($key)));
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

    /**
     * An empty key used to hang encrypt() and decrypt(): the loop that repeats the
     * key until it covers the input doubled an empty string forever.
     *
     * @return iterable<string, array{string, string, ?string, string}>
     */
    public static function emptyKeyProvider(): iterable
    {
        yield 'encrypt with an empty key' => ['encrypt', '', null, 'hello'];
        yield 'encrypt with an empty private key' => ['encrypt', 'abc', '', 'hello'];
        yield 'encrypt nothing with an empty key' => ['encrypt', '', null, ''];
        yield 'decrypt with an empty key' => ['decrypt', '', null, ' B'];
        yield 'decrypt with an empty public key' => ['decrypt', '', 'abc', ' B'];
    }

    #[DataProvider('emptyKeyProvider')]
    public function testAnEmptyKeyIsRefused(string $method, string $public, ?string $private, string $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The key is empty.');

        $this->cipher->$method($input, new Key($public, $private));
    }
}
