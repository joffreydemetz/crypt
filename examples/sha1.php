<?php

/**
 * Example: SHA1-based encryption/decryption
 * 
 * This example demonstrates using the SHA1 cipher with both:
 * 1. Auto-generated keys via Sha1Generator
 * 2. Manual key creation
 */

require_once realpath(__DIR__ . '/../vendor/autoload.php');

use JDZ\Crypt\Crypt;
use JDZ\Crypt\Sha1Cipher;
use JDZ\Crypt\Sha1Generator;
use JDZ\Crypt\Key;

try {
    $text = 'I wanna keep this secret';
    echo "Original text: {$text}\n";
    echo str_repeat('=', 60) . "\n\n";

    // Example 1: Using auto-generated key
    echo "Example 1: Auto-generated key\n";
    echo str_repeat('-', 60) . "\n";

    $keyGen = new Sha1Generator();
    $generatedKey = $keyGen->generateKey();

    $crypt = new Crypt(
        new Sha1Cipher(),
        $generatedKey
    );

    $encrypted = $crypt->encrypt($text);
    echo "Encrypted: {$encrypted}\n";

    $decrypted = $crypt->decrypt($encrypted);
    echo "Decrypted: {$decrypted}\n";
    echo "Match: " . ($text === $decrypted ? 'YES' : 'NO') . "\n\n";

    // Example 2: Using manual key
    echo "Example 2: Manual key creation\n";
    echo str_repeat('-', 60) . "\n";

    $manualKey = new Key(sha1('my-secret-passphrase'));

    $crypt2 = new Crypt(
        new Sha1Cipher(),
        $manualKey
    );

    $encrypted2 = $crypt2->encrypt($text);
    echo "Encrypted: {$encrypted2}\n";

    $decrypted2 = $crypt2->decrypt($encrypted2);
    echo "Decrypted: {$decrypted2}\n";
    echo "Match: " . ($text === $decrypted2 ? 'YES' : 'NO') . "\n\n";

    // Example 3: Same key produces same encryption
    echo "Example 3: Consistency check\n";
    echo str_repeat('-', 60) . "\n";

    $crypt3 = new Crypt(
        new Sha1Cipher(),
        new Key(sha1('my-secret-passphrase'))
    );

    $encrypted3 = $crypt3->encrypt($text);
    echo "Same key produces same encryption: " . ($encrypted2 === $encrypted3 ? 'YES' : 'NO') . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

exit(0);
