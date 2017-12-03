<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Crypt;

/**
 * Crypt Interface
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface CryptInterface
{
  /**
   * Decrypt a data string
   *
   * @param   string  $data  The encrypted string to decrypt
   * @return   string  The decrypted data string
   */
  public function decrypt($data);

  /**
   * Encrypt a data string
   *
   * @param   string  $data  The data string to encrypt
   * @return   string  The encrypted data string.
   */
  public function encrypt($data);
  
  /**
   * Generate a new encryption key object
   *
   * @param   array  $options  Key generation options
   * @return   CryptKey
   */
  public function generateKey(array $options=[]);

  /**
   * Set the encryption key object
   *
   * @param   Key   $key  The key object to set
   * @return   Crypt object for chaining
   */
  public function setKey(CryptKey $key);
}
