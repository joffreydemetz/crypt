<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\GeneratorInterface;
use JDZ\Crypt\Key;

class SimpleGenerator implements GeneratorInterface
{
    private const DEFAULT_LENGTH = 256;
    private const DEFAULT_SALT = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    protected int $length = self::DEFAULT_LENGTH;
    protected string $salt = self::DEFAULT_SALT;

    public function setLength(int $length): void
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('The key length must be at least 1.');
        }

        $this->length = $length;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function generateKey(): Key
    {
        return new Key($this->generatePublicKey());
    }

    protected function generatePublicKey(): string
    {
        $salt = $this->salt;
        $saltLength = \strlen($salt);

        $key = '';
        for ($i = 0; $i < $this->length; $i++) {
            // a CSPRNG: mt_rand() is replayable from its seed (D23)
            $key .= $salt[\random_int(0, $saltLength - 1)];
        }

        return $key;
    }
}
