<?php

/**
 * Example: Performance Benchmark
 * 
 * This example measures and compares the performance of different
 * cipher implementations to help you choose the right one for your use case.
 */

require_once realpath(__DIR__ . '/../vendor/autoload.php');

use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Md5Cipher;
use JDZ\Crypt\Sha1Cipher;
use JDZ\Crypt\Key;

/**
 * Benchmark a cipher implementation
 */
function benchmarkCipher(string $name, $cipher, Key $key, string $text, int $iterations): array
{
    $crypt = new Crypt($cipher, $key);
    
    // Warmup
    $crypt->encrypt($text);
    $crypt->decrypt($crypt->encrypt($text));
    
    // Benchmark encryption
    $startEncrypt = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $encrypted = $crypt->encrypt($text);
    }
    $encryptTime = microtime(true) - $startEncrypt;
    
    // Benchmark decryption
    $startDecrypt = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $crypt->decrypt($encrypted);
    }
    $decryptTime = microtime(true) - $startDecrypt;
    
    $totalTime = $encryptTime + $decryptTime;
    
    return [
        'name' => $name,
        'encrypt' => $encryptTime,
        'decrypt' => $decryptTime,
        'total' => $totalTime,
        'ops_per_sec' => $iterations / $totalTime,
    ];
}

/**
 * Format time in appropriate unit
 */
function formatTime(float $seconds): string
{
    if ($seconds < 0.001) {
        return number_format($seconds * 1000000, 2) . ' μs';
    } elseif ($seconds < 1) {
        return number_format($seconds * 1000, 2) . ' ms';
    } else {
        return number_format($seconds, 3) . ' s';
    }
}

try {
    echo "Performance Benchmark\n";
    echo str_repeat('=', 70) . "\n\n";
    
    $iterations = 10000;
    $passphrase = 'benchmark-passphrase-123';
    
    // Test with different text sizes
    $testCases = [
        'Short' => 'Hello',
        'Medium' => str_repeat('This is a test message. ', 10),
        'Long' => str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 50),
    ];
    
    foreach ($testCases as $size => $text) {
        echo "{$size} text ({" . strlen($text) . " bytes)\n";
        echo str_repeat('-', 70) . "\n";
        
        $results = [];
        
        // Benchmark SimpleCipher
        $results[] = benchmarkCipher(
            'SimpleCipher',
            new SimpleCipher(),
            new Key($passphrase),
            $text,
            $iterations
        );
        
        // Benchmark Md5Cipher
        $results[] = benchmarkCipher(
            'Md5Cipher',
            new Md5Cipher(),
            new Key(md5($passphrase)),
            $text,
            $iterations
        );
        
        // Benchmark Sha1Cipher
        $results[] = benchmarkCipher(
            'Sha1Cipher',
            new Sha1Cipher(),
            new Key(sha1($passphrase)),
            $text,
            $iterations
        );
        
        // Sort by total time (fastest first)
        usort($results, fn($a, $b) => $a['total'] <=> $b['total']);
        
        // Display results
        foreach ($results as $i => $result) {
            $medal = $i === 0 ? '🥇' : ($i === 1 ? '🥈' : '🥉');
            $percent = $i === 0 ? '100%' : number_format(($results[0]['total'] / $result['total']) * 100, 1) . '%';
            
            echo sprintf(
                "%s %-15s | Encrypt: %10s | Decrypt: %10s | Total: %10s | Speed: %s\n",
                $medal,
                $result['name'],
                formatTime($result['encrypt']),
                formatTime($result['decrypt']),
                formatTime($result['total']),
                $percent
            );
        }
        
        echo "\nOperations per second:\n";
        foreach ($results as $result) {
            echo sprintf(
                "  %-15s: %s ops/sec\n",
                $result['name'],
                number_format($result['ops_per_sec'], 0)
            );
        }
        
        echo "\n";
    }
    
    echo str_repeat('=', 70) . "\n";
    echo "Iterations per test: " . number_format($iterations) . "\n\n";
    
    echo "Summary:\n";
    echo "- All three ciphers use the same XOR algorithm, so performance is similar\n";
    echo "- Minor differences are due to key processing and length\n";
    echo "- SimpleCipher uses the raw passphrase (variable length)\n";
    echo "- Md5Cipher uses a 32-character hash\n";
    echo "- Sha1Cipher uses a 40-character hash (slightly slower for long texts)\n";
    echo "- For most use cases, the performance difference is negligible\n";
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}

exit(0);
