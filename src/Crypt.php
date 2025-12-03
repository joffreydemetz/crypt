<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\CipherInterface;
use JDZ\Crypt\Key;

class Crypt
{
  protected CipherInterface $cipher;
  protected Key $key;

  public function __construct(CipherInterface $cipher, Key $key)
  {
    $this->cipher = $cipher;
    $this->key = $key;
  }

  public function decrypt(string $data): string
  {
    return $this->cipher->decrypt($data, $this->key);
  }

  public function encrypt(string $data): string
  {
    return $this->cipher->encrypt($data, $this->key);
  }
}
