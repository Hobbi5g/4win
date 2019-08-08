<?php

	/*** WARNING: DO NOT MODIFY THIS FILE !!! ***/

	/**
	 * installCore encoder for installer parameters
	 *
	 * @version   1.2.1
	 * @copyright ironSource 2014
	 */
	class IC_Encoder {

		// Error codes
		const E_ENCRYPTION  = 100;
		const E_KEY_MISSING = 101;
		const E_KEY_INVALID = 102;

		// List of keys
		private $keys = array();

		/**
		 * Class constructor
		 *
		 * @param string $key_location
		 *
		 * @return IC_Encoder
		 * @throws IC_EncoderException
		 */
		public function __construct($key_location) {
			if (!function_exists('openssl_seal')
				|| !function_exists('openssl_pkey_get_public')) {
				die('OpenSSL is required to use this library! See http://php.net/manual/en/book.openssl.php');
			}
			if (empty($key_location)
				|| !is_file($key_location)
				|| !@is_readable($key_location)) {
				throw new IC_EncoderException("Key file not found at {$key_location}", self::E_KEY_MISSING);
			}

			// Load key
			$key_content = @file_get_contents($key_location);
			if (($key = openssl_pkey_get_public($key_content)) === false) {
				throw new IC_EncoderException("Failed to extract key from file at {$key_location}", self::E_KEY_INVALID);
			}

			$this->keys[] = $key;
		}

		/**
		 * Method encrypts provided data
		 *
		 * @param array $data
		 *
		 * @return array
		 * @throws IC_EncoderException
		 */
		public function encode(array $data) {
			$sealed = '';
			$ekeys = array();
			$data = http_build_query($data);
			if (openssl_seal($data, $sealed, $ekeys, $this->keys) === false) {
				throw new IC_EncoderException('Failed to encrypt provided data', self::E_ENCRYPTION);
			}
			if (empty($ekeys)) {
				throw new IC_EncoderException('No key was generated during encryption', self::E_KEY_MISSING);
			}
			$ekey = reset($ekeys);
			return array(
				'data' => base64_encode($sealed),
				'key'  => base64_encode($ekey)
			);
		}

	}

	class IC_EncoderException extends Exception {};
