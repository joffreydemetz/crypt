<?php

/**
 * Example: Simple encryption/decryption
 * 
 * This example demonstrates using the SimpleCipher, which is the base
 * cipher implementation used by both Md5Cipher and Sha1Cipher.
 */

require_once realpath(__DIR__ . '/../vendor/autoload.php');

use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\SimpleGenerator;
use JDZ\Crypt\Key;

try {
    $text = 'I wanna keep this secret';
    echo "Original text: {$text}\n";
    echo str_repeat('=', 60) . "\n\n";

    // Example 1: Using SimpleGenerator with custom configuration
    echo "Example 1: Auto-generated key with custom length\n";
    echo str_repeat('-', 60) . "\n";

    $keyGen = new SimpleGenerator();
    $keyGen->setLength(128); // Custom key length
    $generatedKey = $keyGen->generateKey();

    $crypt = new Crypt(
        new SimpleCipher(),
        $generatedKey
    );

    $encrypted = $crypt->encrypt($text);
    echo "Encrypted: {$encrypted}\n";

    $decrypted = $crypt->decrypt($encrypted);
    echo "Decrypted: {$decrypted}\n";
    echo "Match: " . ($text === $decrypted ? 'YES' : 'NO') . "\n\n";

    // Example 2: Using a simple string as key
    echo "Example 2: Simple string key\n";
    echo str_repeat('-', 60) . "\n";

    $simpleKey = new Key('my-simple-passphrase');

    $crypt2 = new Crypt(
        new SimpleCipher(),
        $simpleKey
    );

    $encrypted2 = $crypt2->encrypt($text);
    echo "Encrypted: {$encrypted2}\n";

    $decrypted2 = $crypt2->decrypt($encrypted2);
    echo "Decrypted: {$decrypted2}\n";
    echo "Match: " . ($text === $decrypted2 ? 'YES' : 'NO') . "\n\n";

    // Example 3: Different data types
    echo "Example 3: Different text samples\n";
    echo str_repeat('-', 60) . "\n";

    $key = new Key('test-key');
    $crypt3 = new Crypt(new SimpleCipher(), $key);

    $samples = [
        'Short',
        'A longer text with multiple words and spaces',
        'Numbers: 1234567890',
        'Special: !@#$%^&*()',
    ];

    foreach ($samples as $sample) {
        $enc = $crypt3->encrypt($sample);
        $dec = $crypt3->decrypt($enc);
        $match = $sample === $dec ? '✓' : '✗';
        echo "{$match} '{$sample}'\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

exit(0);
