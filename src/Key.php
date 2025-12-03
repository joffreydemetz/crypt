<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

class Key
{
  public string $public;
  public ?string $private;

  public function __construct(string $public, ?string $private = null)
  {
    $this->public = $public;
    $this->private = null !== $private ? $private : $public;
  }
}
