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

  protected function hexToInt(string $hex, int $i): int
  {
    $j = $i * 2;
    $c = substr($hex, $j, 1);
    $c1 = substr($hex, $j + 1, 1);

    $k = 0;

    switch ($c) {
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

    switch ($c1) {
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

  protected function hexToIntArray(string $hex): array
  {
    $array = [];

    $j = \strlen($hex) / 2;

    for ($i = 0; $i < $j; $i++) {
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
