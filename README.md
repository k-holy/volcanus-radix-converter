Volcanus\RadixConverter
===============

[![Latest Stable Version](https://poser.pugx.org/volcanus/radix-converter/v/stable.png)](https://packagist.org/packages/volcanus/radix-converter)
[![Build Status](https://travis-ci.org/k-holy/volcanus-radix-converter.png?branch=master)](https://travis-ci.org/k-holy/volcanus-radix-converter)
[![Coverage Status](https://coveralls.io/repos/k-holy/volcanus-radix-converter/badge.png?branch=master)](https://coveralls.io/r/k-holy/volcanus-radix-converter?branch=master)

##RadixConverter

N進数への変換を行うためのクラスです。
初期設定では62進数に変換します。

	use \Volcanus\RadixConverter\RadixConverter;

	$converter = new RadixConverter();
	$converter->encode(62)->value(); // '10'
	$converter->decode('10')->value(); // '62'

お手軽に静的コールでも使えます。

	RadixConverter::encode(62); // '10'
	RadixConverter::decode('10'); // 62

16進数や36進数の変換にも対応しています。

	$converter->config('map', RadixConverter::MAP_HEXADECIMAL);
	$converter->encode(65535)->value(); // 'ffff'
	$converter->config('map', RadixConverter::MAP_ALPHANUMERIC_36);
	$converter->encode(65535)->value(); // '1ekf'

自分で定義することもできます。（マルチバイト未対応）

	$converter->config('map', 'OKNU');
	$converter->encode(0)->value(); // 'O'
	$converter->encode(4)->value(); // 'KO'
	$converter->encode(16)->value(); // 'KOO'
	$converter->encode(228)->value(); // 'UNKO'

BC Math関数を使える環境でacceptLong設定を有効にすると、PHP_INT_MAX 以上の整数値を文字列として扱えます。

	$converter->config('acceptLong', true);
	$converter->encode('2147483648')->value(); // '2lkCB2'
	$converter->encode('2147483648')->decode()->value(); // '2147483648'
	$converter->encode('9223372036854775808')->value(); // 'aZl8N0y58M8'
	$converter->encode('9223372036854775808')->decode()->value(); // '9223372036854775808'

