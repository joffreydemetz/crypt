<?php

/**
 * Example: Comparing all cipher types
 * 
 * This example demonstrates the differences between SimpleCipher,
 * Md5Cipher, and Sha1Cipher implementations.
 */

require_once realpath(__DIR__ . '/../vendor/autoload.php');

use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Md5Cipher;
use JDZ\Crypt\Sha1Cipher;
use JDZ\Crypt\Key;

try {
    $text = 'Secret message';
    $passphrase = 'my-password-123';

    echo "Cipher Type Comparison\n";
    echo str_repeat('=', 60) . "\n";
    echo "Original text: {$text}\n";
    echo "Passphrase: {$passphrase}\n";
    echo str_repeat('=', 60) . "\n\n";

    // SimpleCipher
    echo "1. SimpleCipher (direct passphrase)\n";
    echo str_repeat('-', 60) . "\n";
    $simpleKey = new Key($passphrase);
    $simpleCrypt = new Crypt(new SimpleCipher(), $simpleKey);

    $simpleEncrypted = $simpleCrypt->encrypt($text);
    $simpleDecrypted = $simpleCrypt->decrypt($simpleEncrypted);

    echo "Encrypted: {$simpleEncrypted}\n";
    echo "Decrypted: {$simpleDecrypted}\n";
    echo "Match: " . ($text === $simpleDecrypted ? 'YES' : 'NO') . "\n\n";

    // Md5Cipher
    echo "2. Md5Cipher (MD5 hashed passphrase)\n";
    echo str_repeat('-', 60) . "\n";
    $md5Key = new Key(md5($passphrase));
    $md5Crypt = new Crypt(new Md5Cipher(), $md5Key);

    $md5Encrypted = $md5Crypt->encrypt($text);
    $md5Decrypted = $md5Crypt->decrypt($md5Encrypted);

    echo "Encrypted: {$md5Encrypted}\n";
    echo "Decrypted: {$md5Decrypted}\n";
    echo "Match: " . ($text === $md5Decrypted ? 'YES' : 'NO') . "\n\n";

    // Sha1Cipher
    echo "3. Sha1Cipher (SHA1 hashed passphrase)\n";
    echo str_repeat('-', 60) . "\n";
    $sha1Key = new Key(sha1($passphrase));
    $sha1Crypt = new Crypt(new Sha1Cipher(), $sha1Key);

    $sha1Encrypted = $sha1Crypt->encrypt($text);
    $sha1Decrypted = $sha1Crypt->decrypt($sha1Encrypted);

    echo "Encrypted: {$sha1Encrypted}\n";
    echo "Decrypted: {$sha1Decrypted}\n";
    echo "Match: " . ($text === $sha1Decrypted ? 'YES' : 'NO') . "\n\n";

    // Comparison
    echo "Comparison\n";
    echo str_repeat('-', 60) . "\n";
    echo "All ciphers use the same base algorithm (XOR)\n";
    echo "The difference is in how the key is processed:\n";
    echo "  - SimpleCipher: Uses passphrase directly\n";
    echo "  - Md5Cipher: Uses MD5 hash of passphrase (32 chars)\n";
    echo "  - Sha1Cipher: Uses SHA1 hash of passphrase (40 chars)\n";
    echo "\nAll produce different encrypted outputs:\n";
    echo "  - Simple: " . (strlen($simpleEncrypted) > 40 ? substr($simpleEncrypted, 0, 40) . '...' : $simpleEncrypted) . "\n";
    echo "  - MD5:    " . (strlen($md5Encrypted) > 40 ? substr($md5Encrypted, 0, 40) . '...' : $md5Encrypted) . "\n";
    echo "  - SHA1:   " . (strlen($sha1Encrypted) > 40 ? substr($sha1Encrypted, 0, 40) . '...' : $sha1Encrypted) . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

exit(0);
