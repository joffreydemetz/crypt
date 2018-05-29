<?php
/**
 * Joffrey Demetz <joffrey.demetz@gmail.com>
 * <https://joffrey.demetz.com>
 */
namespace JDZ\Crypt;

use PHPUnit\Framework\TestCase;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CryptTest extends TestCase
{
  /**
   * Test basic debug
   */
  public function testCrypt()
  {
    $privateKey = md5('oiuoiupoiupo'.@$_SERVER['HTTP_USER_AGENT']);
    $clean      = 'Test value';
    
    // encrypt
    $key    = new CryptKey($privateKey, $privateKey);
    $cipher = new CryptCipher();
    $crypt  = new Crypt($cipher, $key);
    $crypted = $crypt->encrypt($clean);
    
    // decrypt
    $key    = new CryptKey($privateKey, $privateKey);
    $cipher = new CryptCipher();
    $crypt  = new Crypt($cipher, $key);
    $decrypted = $crypt->decrypt($crypted);
    
    $this->assertEquals($clean, $decrypted, 'Decrypted value is not the same as the original string');
  }
}
