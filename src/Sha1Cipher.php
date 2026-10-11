<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Crypt;

use JDZ\Crypt\SimpleCipher;

/**
 * @deprecated since 3.1.0 — an alias of SimpleCipher: nothing is hashed, the
 *             key is used as it is. Kept so what was stored with it decodes
 *             the same; use SimpleCipher.
 */
class Sha1Cipher extends SimpleCipher {}
