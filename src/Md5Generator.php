<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\SimpleGenerator;

class Md5Generator extends SimpleGenerator
{
    protected function generatePublicKey(): string
    {
        return md5(parent::generatePublicKey());
    }
}
