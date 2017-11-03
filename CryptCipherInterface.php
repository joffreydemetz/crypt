<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Crypt;

/**
 * Crypt cipher for Simple encryption, decryption and key generation.
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CryptCipher 
{
	/**
	 * Decrypt a data string
	 *
	 * @param 	string      $data  The encrypted string to decrypt
	 * @param 	CryptKey    $key   The key object to use for decryption
	 * @return 	string      The decrypted data string
	 */
	public function decrypt($data, CryptKey $key);
  
	/**
	 * Encrypt a data string
	 *
	 * @param 	string      $data  The data string to encrypt
	 * @param 	CryptKey    $key   The key object to use for encryption
	 * @return 	string      The encrypted data string
	 */
	public function encrypt($data, CryptKey $key);

	/**
	 * Generate a new encryption key object
	 *
	 * @param 	array  $options  Key generation options
	 * @return 	CryptKey
	 */
	public function generateKey(array $options=[]);
}
