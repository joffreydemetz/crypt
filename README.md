# Crypt
Simple crypt/decypt utility

## Usage

```
use JDZ\Crypt\Crypt;
use JDZ\Crypt\CryptCipher;
use JDZ\Crypt\CryptKey;

$privateKey = md5('oiuoiupoiupo'.@$_SERVER['HTTP_USER_AGENT']);

$clean = 'Test value';

// encrypt
$key    = new CryptKey($privateKey, $privateKey);
$cipher = new CryptCipher();
$crypt  = new Crypt($cipher, $key);

$crypted = $crypt->encrypt($clean);

echo "\n";
echo 'Encrypt'."\n";
echo 'Clear value : '.$clean."\n";
echo 'Crypted value : '.$crypted."\n";

// decrypt
$key    = new CryptKey($privateKey, $privateKey);
$cipher = new CryptCipher();
$crypt  = new Crypt($cipher, $key);

$decrypted = $crypt->decrypt($crypted);

echo "\n";
echo 'Decrypt'."\n";
echo 'Crypted value : '.$crypted."\n";
echo 'Clear value : '.$decrypted."\n";
```
