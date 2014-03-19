<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\RadixConverter;

/**
 * RadixConverter
 *
 * @author k.holy74@gmail.com
 */
class RadixConverter
{

	/** 2進数 **/
	const MAP_BINARY = '01';
	/** 8進数 **/
	const MAP_OCTAL = '01234567';
	/** 10進数 **/
	const MAP_DECIMAL = '0123456789';
	/** 16進数 **/
	const MAP_HEXADECIMAL = '0123456789abcdef';
	/** 36進数 **/
	const MAP_ALPHANUMERIC_36 = '0123456789abcdefghijklmnopqrstuvwxyz';
	/** 62進数 **/
	const MAP_ALPHANUMERIC_62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @var array 設定値
	 */
	private $config;

	/**
	 * @var mixed 値
	 */
	private $value;

	/**
	 * コンストラクタ
	 *
	 * @param array 設定配列
	 */
	public function __construct(array $configurations = array())
	{
		$this->initialize($configurations);
	}

	/**
	 * 新しいインスタンスを生成して返します。
	 *
	 * @param array 設定配列
	 * @return self
	 */
	public static function instance(array $configurations = array())
	{
		return new static($configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param array 設定配列
	 * @return $this
	 */
	public function initialize(array $configurations = array())
	{
		$this->config = array(
			'map' => self::MAP_ALPHANUMERIC_62,
			'acceptLong' => false,
		);
		$this->value = null;
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->config($name, $value);
			}
		}
		return $this;
	}

	/**
	 * 引数1の場合は指定された設定の値を返します。
	 * 引数2の場合は指定された設置の値をセットして$thisを返します。
	 *
	 * acceptLong
	 *     整数の最大値を越える値を扱うかどうかのフラグ。
	 *     PHP_INT_MAX以上の数値を扱う場合、このフラグを有効にする必要があります。
	 *     ※GMP関数またはBcMath関数が必要です
	 *
	 * map
	 *     N進数への変換用文字列
	 *
	 * @param string 設定名
	 * @return mixed 設定値 または $this
	 */
	public function config($name)
	{
		if (!array_key_exists($name, $this->config)) {
			throw new \InvalidArgumentException(
				sprintf('The configuration "%s" does not exists.', $name));
		}
		switch (func_num_args()) {
		case 1:
			return $this->config[$name];
		case 2:
			$value = func_get_arg(1);
			if (isset($value)) {
				switch ($name) {
				case 'map':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The map is invalid type %s.', (is_object($value))
								? get_class($value) : gettype($value)));
					}
					if (strlen($value) !== strlen(count_chars($value, 3))) {
						throw new \InvalidArgumentException(
							sprintf('The map "%s" contains same character.', $value));
					}
					break;
				case 'acceptLong':
					if ($value) {
						if (!extension_loaded('gmp') && !extension_loaded('bcmath')) {
							throw new \RuntimeException('BcMath extension and GMP extension is not loaded.');
						}
					}
					$value = (bool)$value;
					break;
				}
				$this->config[$name] = $value;
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 引数なしの場合は値を返します。
	 * 引数1の場合は変換する文字列を値にセットして$thisを返します。
	 *
	 * @param string 変換する文字列
	 * @return mixed
	 */
	public function value()
	{
		switch (func_num_args()) {
		case 0:
			return $this->value;
		case 1:
			$value = func_get_arg(0);
			if (!is_string($value) && !is_int($value)) {
				throw new \InvalidArgumentException(
					sprintf('The value is invalid type %s.', (is_object($value))
						? get_class($value) : gettype($value)));
			}
			$this->value = $value;
			return $this;
		}
	}

	/**
	 * 値を文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->value;
	}

	/**
	 * インスタンスメソッド encode(), decode()
	 * メソッドチェーンのため$thisを返します。
	 */
	public function __call($method, $args)
	{
		switch ($method) {
		case 'encode':
		case 'decode':
			$value = (isset($args[0])) ? $args[0] : $this->value;
			$map   = (isset($args[1])) ? $args[1] : $this->config('map');
			$acceptLong = (isset($args[2])) ? $args[2] : $this->config('acceptLong');
			$this->value = static::$method($value, $map, $acceptLong);
			return $this;
		}
		throw new \BadMethodCallException(
			sprintf('The method "%s" is not defined.', $method));
	}

	/**
	 * スタティックメソッド encode(), decode()
	 */
	public static function __callStatic($method, $args)
	{
		switch ($method) {
		case 'encode':
		case 'decode':
			switch (count($args)) {
			case 1:
				return static::$method($args[0]);
			case 2:
				return static::$method($args[0], $args[1]);
			case 3:
				return static::$method($args[0], $args[1], $args[2]);
			}
		}
		throw new \BadMethodCallException(
			sprintf('The method "%s" is not defined.', $method));
	}

	/**
	 * 数値を文字列に変換して返します。
	 *
	 * @param mixed   変換する数値
	 * @param string  N進数への変換用文字列 (指定のない場合は 62進数)
	 * @param boolean 整数の最大値を越える値を扱うかどうか (指定のない場合は FALSE) ※GMP関数またはBcMath関数が必要です
	 * @return string
	 */
	private static function encode($number, $map = null, $acceptLong = null)
	{
		if (!isset($map)) {
			$map = self::MAP_ALPHANUMERIC_62;
		}
		if (!isset($acceptLong)) {
			$acceptLong = false;
		}
		$gmpLoaded = extension_loaded('gmp');
		$bcmathLoaded = extension_loaded('bcmath');
		if ($acceptLong && !$gmpLoaded && !$bcmathLoaded) {
			throw new \RuntimeException('GMP extension and BcMath extension is not loaded.');
		}
		if (is_string($number) && !ctype_digit($number)) {
			throw new \InvalidArgumentException(
				sprintf('The value "%s" includes other than number.', $number));
		}
		if (!$acceptLong && (PHP_INT_MAX < floatval($number))) {
			throw new \InvalidArgumentException(
				sprintf('The value "%s" is too large.', $number));
		}
		$string = '';
		$length = strlen($map);
		do {
			if (!$acceptLong) {
				$offset = $number % $length;
				$number = $number / $length;
			} elseif ($gmpLoaded) {
				$offset = gmp_strval(gmp_mod(gmp_init($number, 10), gmp_init($length, 10)), 10);
				$number = gmp_strval(gmp_div_q(gmp_init($number, 10), gmp_init($length, 10)), 10);
			} elseif ($bcmathLoaded) {
				$offset = bcmod($number, $length);
				$number = bcdiv($number, $length);
			}
			$string .= $map[$offset];
		} while ($number >= 1);
		return strrev($string);
	}

	/**
	 * 文字列を数値に変換して返します。
	 * 整数の最大値を越える値を扱う場合は、文字列型で返します。
	 *
	 * @param string  変換する文字列
	 * @param string  N進数への変換用文字列 (指定のない場合は 62進数)
	 * @param boolean 整数の最大値を越える値を扱うかどうか (指定のない場合は FALSE) ※GMP関数またはBcMath関数が必要です
	 * @return mixed string|int
	 */
	private static function decode($string, $map = null, $acceptLong = null)
	{
		if (!isset($map)) {
			$map = self::MAP_ALPHANUMERIC_62;
		}
		if (!isset($acceptLong)) {
			$acceptLong = false;
		}
		$gmpLoaded = extension_loaded('gmp');
		$bcmathLoaded = extension_loaded('bcmath');
		if ($acceptLong && !$gmpLoaded && !$bcmathLoaded) {
			throw new \RuntimeException('GMP extension and BcMath extension is not loaded.');
		}
		$number = 0;
		$length = strlen($map);
		foreach (str_split(strrev($string)) as $i => $char) {
			$n = strpos($map, $char);
			if ($n === false) {
				throw new \InvalidArgumentException(
					sprintf('The value "%s" includes invalid character "%s".', $string, $char));
			}
			if (!$acceptLong) {
				$number += $n * pow($length, $i);
			} elseif ($gmpLoaded) {
				$number = gmp_strval(gmp_add(gmp_init($number, 10), gmp_mul(gmp_init($n, 10), gmp_pow(gmp_init($length, 10), $i))), 10);
			} elseif ($bcmathLoaded) {
				$number = bcadd($number, bcmul($n, bcpow($length, $i)));
			}
		}
		return $number;
	}

}
