<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\SimpleGenerator;

class Sha1Generator extends SimpleGenerator
{
    protected function generatePublicKey(): string
    {
        return sha1(parent::generatePublicKey());
    }
}
