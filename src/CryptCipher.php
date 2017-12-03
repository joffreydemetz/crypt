<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Crypt;

/**
 * Crypt cipher for Simple encryption, decryption and key generation.
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CryptCipher implements CryptCipherInterface
{
  /**
   * {@inheritDoc}
   */
  public function decrypt($data, CryptKey $key)
  {
    $decrypted = '';
    $tmp = $key->public;

    // Convert the HEX input into an array of integers and get the number of characters.
    $chars = $this->hexToIntArray($data);
    $charCount = count($chars);

    // Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
    for($i=0; $i<$charCount; $i=strlen($tmp)){
      $tmp = $tmp . $tmp;
    }

    // Get the XOR values between the ASCII values of the input and key characters for all input offsets.
    for($i=0; $i<$charCount; $i++){
      $decrypted .= chr($chars[$i] ^ ord($tmp[$i]));
    }

    return $decrypted;
  }
  
  /**
   * {@inheritDoc}
   */
  public function encrypt($data, CryptKey $key)
  {
    $encrypted = '';
    $tmp = $key->private;

    // Split up the input into a character array and get the number of characters.
    $chars = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);
    $charCount = count($chars);

    // Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
    for ($i = 0; $i < $charCount; $i = strlen($tmp)){
      $tmp = $tmp . $tmp;
    }

    // Get the XOR values between the ASCII values of the input and key characters for all input offsets.
    for ($i = 0; $i < $charCount; $i++){
      $encrypted .= $this->intToHex(ord($tmp[$i]) ^ ord($chars[$i]));
    }

    return $encrypted;
  }

  /**
   * {@inheritDoc}
   */
  public function generateKey(array $options=[])
  {
    // Create the new encryption key object.
    $key = new CryptKey();
    
    // Just a random key of a given length.
    $key->private = $this->getRandomKey();
    $key->public  = $key->private;

    return $key;
  }

  /**
   * Generate a random key of a given length.
   *
   * @param   integer  $length  The length of the key to generate.
   * @return   string
   */
  protected function getRandomKey($length = 256)
  {
    $key = '';
    $salt = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $saltLength = strlen($salt);

    for ($i = 0; $i < $length; $i++){
      $key .= $salt[mt_rand(0, $saltLength - 1)];
    }
    
    return $key;
  }

  /**
   * Convert hex to an integer
   *
   * @param   string  $s  The hex string to convert.
   * @param   int     $i  The offset?
   * @return   int
   */
  protected function hexToInt($s, $i)
  {
    $j = (int) $i * 2;
    $k = 0;
    $s1 = (string) $s;

    // Get the character at position $j.
    $c = substr($s1, $j, 1);

    // Get the character at position $j + 1.
    $c1 = substr($s1, $j + 1, 1);

    switch ($c)
    {
      case 'A':
        $k += 160;
        break;
      case 'B':
        $k += 176;
        break;
      case 'C':
        $k += 192;
        break;
      case 'D':
        $k += 208;
        break;
      case 'E':
        $k += 224;
        break;
      case 'F':
        $k += 240;
        break;
      case ' ':
        $k += 0;
        break;
      default:
        (int) $k = $k + (16 * (int) $c);
        break;
    }

    switch ($c1)
    {
      case 'A':
        $k += 10;
        break;
      case 'B':
        $k += 11;
        break;
      case 'C':
        $k += 12;
        break;
      case 'D':
        $k += 13;
        break;
      case 'E':
        $k += 14;
        break;
      case 'F':
        $k += 15;
        break;
      case ' ':
        $k += 0;
        break;
      default:
        $k += (int) $c1;
        break;
    }

    return $k;
  }

  /**
   * Convert hex to an array of integers
   *
   * @param   string  $hex  The hex string to convert to an integer array
   * @return   array   An array of integers
   */
  protected function hexToIntArray($hex)
  {
    $array = [];

    $j = (int) strlen($hex) / 2;

    for ($i = 0; $i < $j; $i++){
      $array[$i] = (int) $this->hexToInt($hex, $i);
    }

    return $array;
  }

  /**
   * Convert an integer to a hexadecimal string
   *
   * @param   int     $i  An integer value to convert to a hex string
   * @return   string 
   */
  protected function intToHex($i)
  {
    $i = (int) $i;

    // Get the first character of the hexadecimal string if there is one.
    $j = (int) ($i / 16);
    if ($j === 0){
      $s = ' ';
    }
    else {
      $s = strtoupper(dechex($j));
    }

    // Get the second character of the hexadecimal string.
    $k = $i - $j * 16;
    $s = $s . strtoupper(dechex($k));

    return $s;
  }
}
