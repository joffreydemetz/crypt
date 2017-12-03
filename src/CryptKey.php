<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Crypt;

/**
 * Base encryption key
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CryptKey
{
  /**
   * The private key
   * 
   * @var   string
   */
  public $private;

  /**
   * The public key
   * 
   * @var   string 
   */
  public $public;

  /**
   * Constructor
   *
   * @param   string  $private  The private key
   * @param   string  $public   The public key
   */
  public function __construct($private=null, $public=null)
  {
    $this->private = isset($private) ? (string) $private : null;
    $this->public  = isset($public)  ? (string) $public  : null;
  }
}
