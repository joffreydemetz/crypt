<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\Key;

interface CipherInterface
{
  public function encrypt(string $decrypted, Key $key): string;

  public function decrypt(string $encrypted, Key $key): string;
}
