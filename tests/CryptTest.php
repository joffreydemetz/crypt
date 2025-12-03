<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt\Tests;

use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Key;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    public function testConstructorAcceptsCipherAndKey(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('test-key');
        $crypt = new Crypt($cipher, $key);

        $this->assertInstanceOf(Crypt::class, $crypt);
    }

    public function testEncryptReturnsString(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('test-key');
        $crypt = new Crypt($cipher, $key);

        $result = $crypt->encrypt('test data');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testDecryptReturnsString(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('test-key');
        $crypt = new Crypt($cipher, $key);

        $encrypted = $crypt->encrypt('test data');
        $result = $crypt->decrypt($encrypted);

        $this->assertIsString($result);
    }

    public function testEncryptDecryptRoundTrip(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('secret-key');
        $crypt = new Crypt($cipher, $key);

        $originalData = 'This is a secret message!';
        $encrypted = $crypt->encrypt($originalData);
        $decrypted = $crypt->decrypt($encrypted);

        $this->assertNotEquals($originalData, $encrypted);
        $this->assertSame($originalData, $decrypted);
    }

    public function testMultipleEncryptDecryptCycles(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('cycle-key');
        $crypt = new Crypt($cipher, $key);

        $originalData = 'Cycle test data';

        // Multiple encrypt-decrypt cycles should work
        for ($i = 0; $i < 5; $i++) {
            $encrypted = $crypt->encrypt($originalData);
            $decrypted = $crypt->decrypt($encrypted);
            $this->assertSame($originalData, $decrypted);
        }
    }

    public function testEncryptDecryptWithDifferentDataTypes(): void
    {
        $cipher = new SimpleCipher();
        $key = new Key('data-type-key');
        $crypt = new Crypt($cipher, $key);

        $testCases = [
            'simple text',
            'Text with numbers 12345',
            'Special chars: !@#$%',
            '',
            'Line1\nLine2\nLine3',
            'Tab\tseparated\tvalues',
        ];

        foreach ($testCases as $testData) {
            $encrypted = $crypt->encrypt($testData);
            $decrypted = $crypt->decrypt($encrypted);
            $this->assertSame($testData, $decrypted, "Failed for: {$testData}");
        }
    }

    public function testDifferentInstancesWithSameKeyProduceSameResults(): void
    {
        $cipher1 = new SimpleCipher();
        $key1 = new Key('shared-key');
        $crypt1 = new Crypt($cipher1, $key1);

        $cipher2 = new SimpleCipher();
        $key2 = new Key('shared-key');
        $crypt2 = new Crypt($cipher2, $key2);

        $originalData = 'Shared encryption test';
        $encrypted1 = $crypt1->encrypt($originalData);
        $encrypted2 = $crypt2->encrypt($originalData);

        // Same key should produce same encryption
        $this->assertSame($encrypted1, $encrypted2);

        // Each instance should be able to decrypt the other's output
        $this->assertSame($originalData, $crypt1->decrypt($encrypted2));
        $this->assertSame($originalData, $crypt2->decrypt($encrypted1));
    }

    public function testDifferentKeysCannotDecryptEachOther(): void
    {
        $cipher1 = new SimpleCipher();
        $key1 = new Key('key-one');
        $crypt1 = new Crypt($cipher1, $key1);

        $cipher2 = new SimpleCipher();
        $key2 = new Key('key-two');
        $crypt2 = new Crypt($cipher2, $key2);

        $originalData = 'Different keys test';
        $encrypted1 = $crypt1->encrypt($originalData);

        // Decrypting with different key should not produce original data
        $decrypted2 = $crypt2->decrypt($encrypted1);
        $this->assertNotEquals($originalData, $decrypted2);
    }
}
