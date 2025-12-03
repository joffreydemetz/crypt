# Crypt
Simple crypt/decrypt utility

## Installation
To install the package, you can use Composer:

```bash
composer require jdz/crypt
```

## Requirements
- PHP 8.1 or higher
- Composer for dependency management

## Usage

### Basic Example

```php
use JDZ\Crypt\Crypt;
use JDZ\Crypt\SimpleCipher;
use JDZ\Crypt\Key;

// Create a key
$key = new Key('my-secret-passphrase');

// Create cipher and crypt instances
$cipher = new SimpleCipher();
$crypt = new Crypt($cipher, $key);

// Encrypt
$originalText = 'Secret message';
$encrypted = $crypt->encrypt($originalText);

// Decrypt
$decrypted = $crypt->decrypt($encrypted);

echo $decrypted; // Output: Secret message
```

### Available Ciphers

The library provides three cipher implementations:

1. **SimpleCipher** - Uses the passphrase directly as the encryption key
2. **Md5Cipher** - Uses MD5 hash of the passphrase (32 characters)
3. **Sha1Cipher** - Uses SHA1 hash of the passphrase (40 characters)

All ciphers use XOR-based encryption and extend from `SimpleCipher`.

### Key Generation

You can generate keys automatically using the generator classes:

```php
use JDZ\Crypt\SimpleGenerator;
use JDZ\Crypt\Md5Generator;
use JDZ\Crypt\Sha1Generator;

// Simple generator
$generator = new SimpleGenerator();
$key = $generator->generateKey();

// MD5 generator
$md5Generator = new Md5Generator();
$md5Key = $md5Generator->generateKey();

// SHA1 generator
$sha1Generator = new Sha1Generator();
$sha1Key = $sha1Generator->generateKey();
```

### Manual Key Creation

```php
use JDZ\Crypt\Key;

// Single key (public and private are the same)
$key = new Key('my-passphrase');

// Separate public and private keys
$key = new Key('public-key', 'private-key');
```

## Examples

The `examples/` directory contains:

- **simple.php** - Basic usage with SimpleCipher and key generation
- **md5.php** - MD5-based encryption with auto-generated and manual keys
- **sha1.php** - SHA1-based encryption with auto-generated and manual keys
- **comparison.php** - Comparison of all cipher types
- **benchmark.php** - Performance comparison and timing benchmarks

Run an example:
```bash
php examples/simple.php
php examples/md5.php
php examples/sha1.php
```

Compare and benchmark:
```bash
php examples/comparison.php
php examples/benchmark.php
```
## Testing

### Run Tests

Run all tests:

```bash
composer test
# or
vendor/bin/phpunit
```

Run tests with coverage report (HTML):

```bash
composer test-coverage
# Coverage report will be generated in the coverage/ directory
```

### Test Structure

Tests are located in the `tests/` directory and follow the PSR-4 autoloading standard under the `JDZ\Crypt\Tests` namespace.

- `KeyTest.php` - Tests for the Key class
- `SimpleCipherTest.php` - Tests for the SimpleCipher implementation
- `CryptTest.php` - Tests for the main Crypt class

For more details on testing, see [TESTING.md](TESTING.md).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
