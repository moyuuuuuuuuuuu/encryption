<?php

namespace encryption;


class Encryption
{

	/**
	 * 偏移量标识
	 * @var string[]
	 */
	protected static $tagMap = [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p'
	];

	/**
	 * 加密
	 * @param string $original
	 * @param int $offset
	 * @return string
	 */
	public static function encrypt(string $original = '', int $offset = null)
	{
		if (empty($original)) {
			return $original;
		}
		$len = strlen($original);
		$encrypt = '';
		abs($offset) ?? rand(1, 15);

		for ($i = 0; $i < $len; $i++) {
			$ascii = ord($original[$i]) + $offset;
			$encrypt .= chr($ascii);
		}

		$eq = $offset * $offset;
		$length = strlen($eq);

		$base = base64_encode($encrypt);
		$base = str_replace(array('+', '/'), array('-', '_'), $base);

		$prefix = self::$tagMap[$length];
		if ($length % rand(2, 3) == 0) {
			$prefix = strtoupper($prefix);
		}

		if (substr($base, -1, 1) != '=') {
			return $prefix . $base . $eq;
		} else {
			return $prefix . $eq . $base;
		}
	}

	/**
	 * 解密
	 * @param string $hash
	 * @return string
	 */
	public static function decrypt(string $hash = '')
	{
		if (empty($hash)) {
			return $hash;
		}

		$eqLength = array_search(strtolower(substr($hash, 0, 1)), self::$tagMap);

		if (substr($hash, -1, 1) != '=') {
			$number = substr($hash, -($eqLength), $eqLength);
			$original = substr($hash, 1, strlen($hash) - ($eqLength + 1));
		} else {
			$number = substr($hash, 1, $eqLength);
			$original = substr($hash, 1 + $eqLength);
		}
		$offset = sqrt($number);
		$original = str_replace(array('-', '_'), array('+', '/'), $original);
		$mod4 = strlen($original) % 4;
		if ($mod4) {
			$original .= substr('====', $mod4);
		}
		$original = base64_decode($original);

		$len = strlen($original);
		$string = '';
		for ($i = 0; $i < $len; $i++) {
			$ascii = ord($original[$i]) - $offset;
			$string .= chr($ascii);
		}

		return $string;
	}
}
