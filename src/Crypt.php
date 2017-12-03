<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Crypt;

/**
 * Crypt
 * 
 * Handles basic encryption/decryption of data.
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Crypt implements CryptInterface
{
  /**
   * The encryption cipher object
   * 
   * @var    Cipher 
   */
  protected $cipher;
  
  /**
   * The encryption key[/pair)]
   * 
   * @var    CryptKey 
   */
  protected $key;
  
  /**
   * Generate random bytes.
   *
   * @param   int     $length  Length of the random data to generate
   * @return   string  Random binary data
   * @deprecated
   */
  public static function genRandomBytes($length=16)
  {
    $sslStr = openssl_random_pseudo_bytes($length, $strong);
    
    // if a strong cryptology algortithm was used return the generated 
    // string right away.
    if ( $strong ){
      return $sslStr;
    }
    
    // Collect any entropy available in the system along with a number
    // of time measurements of operating system randomness.
    $bitsPerRound = 2;
    $maxTimeMicro = 400;
    $shaHashLength = 20;
    $randomStr = '';
    $total = $length;

    // Check if we can use /dev/urandom.
    $urandom = false;
    $handle  = null;

    // PHP 5.3.3 and up
    if ( function_exists('stream_set_read_buffer') && @is_readable('/dev/urandom') ){
      $handle = @fopen('/dev/urandom', 'rb');
      if ( $handle ){ 
        $urandom = true;
      }
    }
    
    while ($length > strlen($randomStr)){
      $bytes = ($total > $shaHashLength)? $shaHashLength : $total;
      $total -= $bytes;
      
      // Collect any entropy available from the PHP system and filesystem.
      // If we have ssl data that isn't strong, we use it once.
      $entropy  = rand() . uniqid(mt_rand(), true) . $sslStr;
      $entropy .= implode('', @fstat(fopen(__FILE__, 'r')));
      $entropy .= memory_get_usage();
      
      $sslStr = '';
      
      if ( $urandom ){
        stream_set_read_buffer($handle, 0);
        $entropy .= @fread($handle, $bytes);
      } else {
        // There is no external source of entropy so we repeat calls
        // to mt_rand until we are assured there's real randomness in
        // the result.
        // Measure the time that the operations will take on average.
        $samples = 3;
        $duration = 0;
        for($pass = 0; $pass < $samples; ++$pass){
          $microStart = microtime(true) * 1000000;
          $hash = sha1(mt_rand(), true);
          for($count = 0; $count < 50; ++$count){
            $hash = sha1($hash, true);
          }
          $microEnd = microtime(true) * 1000000;
          $entropy .= $microStart . $microEnd;
          if ( $microStart > $microEnd ){
            $microEnd += 1000000;
          }
          $duration += $microEnd - $microStart;
        }
        $duration = $duration / $samples;

        // Based on the average time, determine the total rounds so that
        // the total running time is bounded to a reasonable number.
        $rounds = (int) (($maxTimeMicro / $duration) * 50);

        // Take additional measurements. On average we can expect
        // at least $bitsPerRound bits of entropy from each measurement.
        $iter = $bytes * (int) ceil(8 / $bitsPerRound);
        for($pass = 0; $pass < $iter; ++$pass){
          $microStart = microtime(true);
          $hash = sha1(mt_rand(), true);
          for($count = 0; $count < $rounds; ++$count){
            $hash = sha1($hash, true);
          }
          $entropy .= $microStart . microtime(true);
        }
      }
      
      $randomStr .= sha1($entropy, true);
    }

    if ( $urandom ){
      @fclose($handle);
    }

    return substr($randomStr, 0, $length);
  }
  
  /**
   * A timing safe comparison method. This defeats hacking
   * attempts that use timing based attack vectors.
   *
   * @param   string  $known    A known string to check against
   * @param   string  $unknown  An unknown string to check
   * @return   boolean  True if the two strings are exactly the same
   * @deprecated
   */
  public static function timingSafeCompare($known, $unknown)
  {
    // Prevent issues if string length is 0
    $known   .= chr(0);
    $unknown .= chr(0);
    
    $knownLength   = strlen($known);
    $unknownLength = strlen($unknown);
    
    // Set the result to the difference between the lengths
    $result = $knownLength - $unknownLength;
    
    // Note that we ALWAYS iterate over the user-supplied length to prevent leaking length info.
    for ($i = 0; $i < $unknownLength; $i++){
      // Using % here is a trick to prevent notices. It's safe, since if the lengths are different, $result is already non-0
      $result |= (ord($known[$i % $knownLength]) ^ ord($unknown[$i]));
    }
    
    // They are only identical strings if $result is exactly 0...
    return $result === 0;
  }
  
  /**
   * {@inheritDoc}
   */
  public function __construct(CryptCipher $cipher, CryptKey $key)
  {
    $this->cipher = $cipher;
    $this->key    = $key;
  }
  
  /**
   * {@inheritDoc}
   */
  public function decrypt($data)
  {
    return $this->cipher->decrypt($data, $this->key);
  }

  /**
   * {@inheritDoc}
   */
  public function encrypt($data)
  {
    return $this->cipher->encrypt($data, $this->key);
  }

  /**
   * {@inheritDoc}
   */
  public function generateKey(array $options=[])
  {
    return $this->cipher->generateKey($options);
  }

  /**
   * {@inheritDoc}
   */
  public function setKey(CryptKey $key)
  {
    $this->key = $key;

    return $this;
  }
}
