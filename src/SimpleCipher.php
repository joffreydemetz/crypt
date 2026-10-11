<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\Key;
use JDZ\Crypt\CipherInterface;

class SimpleCipher implements CipherInterface
{
  public function encrypt(string $decrypted, Key $key): string
  {
    $tmp = $key->private;

    if ('' === $tmp) {
      throw new \InvalidArgumentException('The key is empty.');
    }

    // Split up the input into a character array 
    // and get the number of characters.
    $chars = \preg_split('//', $decrypted, -1, \PREG_SPLIT_NO_EMPTY);
    $charCount = count($chars);

    // Repeat the key as many times as necessary to ensure 
    // that the key is at least as long as the input.
    for ($i = 0; $i < $charCount; $i = \strlen($tmp)) {
      $tmp = $tmp . $tmp;
    }

    // Get the XOR values between the ASCII values of the input 
    // and key characters for all input offsets.
    $encrypted = '';
    for ($i = 0; $i < $charCount; $i++) {
      $encrypted .= $this->intToHex(ord($tmp[$i]) ^ ord($chars[$i]));
    }

    return $encrypted;
  }

  public function decrypt(string $encrypted, Key $key): string
  {
    $tmp = $key->public;

    if ('' === $tmp) {
      throw new \InvalidArgumentException('The key is empty.');
    }

    // Convert the HEX input into an array of integers 
    // and get the number of characters.
    $chars = $this->hexToIntArray($encrypted);
    $charCount = count($chars);

    // Repeat the key as many times as necessary to ensure 
    // that the key is at least as long as the input.
    for ($i = 0; $i < $charCount; $i = \strlen($tmp)) {
      $tmp = $tmp . $tmp;
    }

    // Get the XOR values between the ASCII values of the input 
    // and key characters for all input offsets.
    $decrypted = '';
    for ($i = 0; $i < $charCount; $i++) {
      $decrypted .= chr($chars[$i] ^ ord($tmp[$i]));
    }

    return $decrypted;
  }

  /**
   * One byte of the ciphertext: two hex digits, upper or lower case, the
   * high nibble written as a space when it is zero (what intToHex() emits).
   *
   * @throws \InvalidArgumentException on anything else (D23: a tampered value
   *                                   used to decode to garbage)
   */
  protected function hexToInt(string $hex, int $i): int
  {
    $j = $i * 2;
    $high = $hex[$j];
    $low = $hex[$j + 1];

    if (' ' === $high) {
      $high = '0';
    }

    if (1 !== \preg_match('/^[0-9A-Fa-f]{2}$/', $high . $low)) {
      throw new \InvalidArgumentException('The input is not a valid ciphertext.');
    }

    return (int) \hexdec($high . $low);
  }

  /**
   * @throws \InvalidArgumentException on an odd length or a non-hex character
   */
  protected function hexToIntArray(string $hex): array
  {
    $length = \strlen($hex);

    if (0 !== $length % 2) {
      throw new \InvalidArgumentException('The input is not a valid ciphertext.');
    }

    $array = [];

    for ($i = 0, $j = $length / 2; $i < $j; $i++) {
      $array[$i] = $this->hexToInt($hex, $i);
    }

    return $array;
  }

  protected function intToHex(int $i): string
  {
    $j = (int) ($i / 16);
    if (0 === $j) {
      $s = ' ';
    } else {
      $s = \strtoupper(dechex($j));
    }

    // Get the second character of the hexadecimal string.
    $k = $i - $j * 16;
    $s = $s . \strtoupper(dechex($k));

    return $s;
  }
}
