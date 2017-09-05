<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\RadixConverter\Test;

use Volcanus\RadixConverter\RadixConverter;

/**
 * RadixConverterTest
 *
 * @author k.holy74@gmail.com
 */
class RadixConverterTest extends \PHPUnit\Framework\TestCase
{

    /** @var \Volcanus\RadixConverter\RadixConverter */
    protected $converter;

	public function setUp()
	{
		$this->converter = new RadixConverter();
	}

	public function testValueForEncode()
	{
		$this->converter->value(99);
		$this->assertEquals(99, $this->converter->value());
	}

	public function testValueForDecode()
	{
		$this->converter->value('ABC');
		$this->assertEquals('ABC', $this->converter->value());
	}

	public function testEncode()
	{
        $this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('a', $this->converter->encode(10)->value());
		$this->assertEquals('10', $this->converter->encode(62)->value());
		$this->assertEquals('47', $this->converter->encode(255)->value());
		$this->assertEquals('h31', $this->converter->encode(65535)->value());
		$this->assertEquals('2lkCB1', $this->converter->encode(2147483647)->value());
	}

	public function testDecode()
	{
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(10, $this->converter->decode('a')->value());
		$this->assertEquals(62, $this->converter->decode('10')->value());
		$this->assertEquals(255, $this->converter->decode('47')->value());
		$this->assertEquals(65535, $this->converter->decode('h31')->value());
		$this->assertEquals(2147483647, $this->converter->decode('2lkCB1')->value());
	}

	public function testEncodeByCallStatic()
	{
		$this->assertEquals('0', RadixConverter::encode(0));
		$this->assertEquals('1', RadixConverter::encode(1));
		$this->assertEquals('a', RadixConverter::encode(10));
		$this->assertEquals('10', RadixConverter::encode(62));
		$this->assertEquals('47', RadixConverter::encode(255));
		$this->assertEquals('h31', RadixConverter::encode(65535));
		$this->assertEquals('2lkCB1', RadixConverter::encode(2147483647));
	}

	public function testDecodeByCallStatic()
	{
		$this->assertEquals(0, RadixConverter::decode('0'));
		$this->assertEquals(1, RadixConverter::decode('1'));
		$this->assertEquals(10, RadixConverter::decode('a'));
		$this->assertEquals(62, RadixConverter::decode('10'));
		$this->assertEquals(255, RadixConverter::decode('47'));
		$this->assertEquals(65535, RadixConverter::decode('h31'));
		$this->assertEquals(2147483647, RadixConverter::decode('2lkCB1'));
	}

	public function testEncodeAcceptLongOn32Bit()
	{
		$this->converter->config('acceptLong', true);
		$this->assertEquals('2lkCB2', $this->converter->encode('2147483648')->value());
	}

	public function testDecodeAcceptLongOn32Bit()
	{
		$this->converter->config('acceptLong', true);
		$this->assertEquals('2147483648', $this->converter->decode('2lkCB2')->value());
	}

	public function testEncodeAcceptLongOn64Bit()
	{
		$this->converter->config('acceptLong', true);
		$this->assertEquals('aZl8N0y58M8', $this->converter->encode('9223372036854775808')->value());
	}

	public function testDecodeAcceptLongOn64Bit()
	{
		$this->converter->config('acceptLong', true);
		$this->assertEquals('9223372036854775808', $this->converter->decode('aZl8N0y58M8')->value());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenNotAcceptLong()
	{
		$this->converter->config('acceptLong', false)->encode(strval(PHP_INT_MAX + 1));
	}

	public function testToString()
	{
		$this->assertEquals('10', (string)$this->converter->encode(62));
		$this->assertEquals('62', (string)$this->converter->decode('10'));
	}

	public function testBinaryEncode()
	{
		$this->converter->config('map', RadixConverter::MAP_BINARY);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('10', $this->converter->encode(2)->value());
		$this->assertEquals('1010', $this->converter->encode(10)->value());
		$this->assertEquals('11111111', $this->converter->encode(255)->value());
		$this->assertEquals('1111111111111111', $this->converter->encode(65535)->value());
		$this->assertEquals('1111111111111111111111111111111', $this->converter->encode(2147483647)->value());
	}

	public function testBinaryDecode()
	{
		$this->converter->config('map', RadixConverter::MAP_BINARY);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(2, $this->converter->decode('10')->value());
		$this->assertEquals(10, $this->converter->decode('1010')->value());
		$this->assertEquals(255, $this->converter->decode('11111111')->value());
		$this->assertEquals(65535, $this->converter->decode('1111111111111111')->value());
		$this->assertEquals(2147483647, $this->converter->decode('1111111111111111111111111111111')->value());
	}

	public function testOctalEncode()
	{
		$this->converter->config('map', RadixConverter::MAP_OCTAL);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('10', $this->converter->encode(8)->value());
		$this->assertEquals('12', $this->converter->encode(10)->value());
		$this->assertEquals('377', $this->converter->encode(255)->value());
		$this->assertEquals('177777', $this->converter->encode(65535)->value());
		$this->assertEquals('17777777777', $this->converter->encode(2147483647)->value());
	}

	public function testOctalDecode()
	{
		$this->converter->config('map', RadixConverter::MAP_OCTAL);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(8, $this->converter->decode('10')->value());
		$this->assertEquals(10, $this->converter->decode('12')->value());
		$this->assertEquals(255, $this->converter->decode('377')->value());
		$this->assertEquals(65535, $this->converter->decode('177777')->value());
		$this->assertEquals(2147483647, $this->converter->decode('17777777777')->value());
	}

	public function testDecimalEncode()
	{
		$this->converter->config('map', RadixConverter::MAP_DECIMAL);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('10', $this->converter->encode(10)->value());
		$this->assertEquals('255', $this->converter->encode(255)->value());
		$this->assertEquals('65535', $this->converter->encode(65535)->value());
		$this->assertEquals('2147483647', $this->converter->encode(2147483647)->value());
	}

	public function testDecimalDecode()
	{
		$this->converter->config('map', RadixConverter::MAP_DECIMAL);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(10, $this->converter->decode('10')->value());
		$this->assertEquals(255, $this->converter->decode('255')->value());
		$this->assertEquals(65535, $this->converter->decode('65535')->value());
		$this->assertEquals(2147483647, $this->converter->decode('2147483647')->value());
	}

	public function testHexadecimalEncode()
	{
		$this->converter->config('map', RadixConverter::MAP_HEXADECIMAL);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('a', $this->converter->encode(10)->value());
		$this->assertEquals('10', $this->converter->encode(16)->value());
		$this->assertEquals('ff', $this->converter->encode(255)->value());
		$this->assertEquals('ffff', $this->converter->encode(65535)->value());
		$this->assertEquals('7fffffff', $this->converter->encode(2147483647)->value());
	}

	public function testHexadecimalDecode()
	{
		$this->converter->config('map', RadixConverter::MAP_HEXADECIMAL);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(10, $this->converter->decode('a')->value());
		$this->assertEquals(16, $this->converter->decode('10')->value());
		$this->assertEquals(255, $this->converter->decode('ff')->value());
		$this->assertEquals(65535, $this->converter->decode('ffff')->value());
		$this->assertEquals(2147483647, $this->converter->decode('7fffffff')->value());
	}

	public function testAlphanumeric36Encode()
	{
		$this->converter->config('map', RadixConverter::MAP_ALPHANUMERIC_36);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('a', $this->converter->encode(10)->value());
		$this->assertEquals('10', $this->converter->encode(36)->value());
		$this->assertEquals('73', $this->converter->encode(255)->value());
		$this->assertEquals('1ekf', $this->converter->encode(65535)->value());
		$this->assertEquals('zik0zj', $this->converter->encode(2147483647)->value());
	}

	public function testAlphanumeric36Decode()
	{
		$this->converter->config('map', RadixConverter::MAP_ALPHANUMERIC_36);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(10, $this->converter->decode('a')->value());
		$this->assertEquals(36, $this->converter->decode('10')->value());
		$this->assertEquals(255, $this->converter->decode('73')->value());
		$this->assertEquals(65535, $this->converter->decode('1ekf')->value());
		$this->assertEquals(2147483647, $this->converter->decode('zik0zj')->value());
	}

	public function testAlphanumeric62Encode()
	{
		$this->converter->config('map', RadixConverter::MAP_ALPHANUMERIC_62);
		$this->assertEquals('0', $this->converter->encode(0)->value());
		$this->assertEquals('1', $this->converter->encode(1)->value());
		$this->assertEquals('a', $this->converter->encode(10)->value());
		$this->assertEquals('10', $this->converter->encode(62)->value());
		$this->assertEquals('47', $this->converter->encode(255)->value());
		$this->assertEquals('h31', $this->converter->encode(65535)->value());
		$this->assertEquals('2lkCB1', $this->converter->encode(2147483647)->value());
	}

	public function testAlphanumeric62Decode()
	{
		$this->converter->config('map', RadixConverter::MAP_ALPHANUMERIC_62);
		$this->assertEquals(0, $this->converter->decode('0')->value());
		$this->assertEquals(1, $this->converter->decode('1')->value());
		$this->assertEquals(10, $this->converter->decode('a')->value());
		$this->assertEquals(62, $this->converter->decode('10')->value());
		$this->assertEquals(255, $this->converter->decode('47')->value());
		$this->assertEquals(65535, $this->converter->decode('h31')->value());
		$this->assertEquals(2147483647, $this->converter->decode('2lkCB1')->value());
	}

	public function testEncodeByCustomizedMap()
	{
		$this->converter->config('map', 'OKNU');
		$this->assertEquals('O', $this->converter->encode(0)->value());
		$this->assertEquals('KO', $this->converter->encode(4)->value());
		$this->assertEquals('KOO', $this->converter->encode(16)->value());
		$this->assertEquals('UNKO', $this->converter->encode(228)->value());
	}

	public function testDecodeByCustomizedMap()
	{
		$this->converter->config('map', 'OKNU');
		$this->assertEquals(0, $this->converter->decode('O')->value());
		$this->assertEquals(4, $this->converter->decode('KO')->value());
		$this->assertEquals(16, $this->converter->decode('KOO')->value());
		$this->assertEquals(228, $this->converter->decode('UNKO')->value());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenCustomizedMapContainsSameCharacter()
	{
		$this->converter->config('map', '00123456789');
	}

	public function testEncodeByCallStaticWithMap()
	{
		$this->assertEquals('10', RadixConverter::encode(2, RadixConverter::MAP_BINARY));
		$this->assertEquals('10', RadixConverter::encode(8, RadixConverter::MAP_OCTAL));
		$this->assertEquals('10', RadixConverter::encode(10, RadixConverter::MAP_DECIMAL));
		$this->assertEquals('10', RadixConverter::encode(16, RadixConverter::MAP_HEXADECIMAL));
		$this->assertEquals('10', RadixConverter::encode(36, RadixConverter::MAP_ALPHANUMERIC_36));
		$this->assertEquals('10', RadixConverter::encode(62, RadixConverter::MAP_ALPHANUMERIC_62));
		$this->assertEquals('UNKO', RadixConverter::encode(228, 'OKNU'));
	}

	public function testDecodeByCallStaticWithMap()
	{
		$this->assertEquals(2, RadixConverter::decode('10', RadixConverter::MAP_BINARY));
		$this->assertEquals(8, RadixConverter::decode('10', RadixConverter::MAP_OCTAL));
		$this->assertEquals(10, RadixConverter::decode('10', RadixConverter::MAP_DECIMAL));
		$this->assertEquals(16, RadixConverter::decode('10', RadixConverter::MAP_HEXADECIMAL));
		$this->assertEquals(36, RadixConverter::decode('10', RadixConverter::MAP_ALPHANUMERIC_36));
		$this->assertEquals(62, RadixConverter::decode('10', RadixConverter::MAP_ALPHANUMERIC_62));
		$this->assertEquals(228, RadixConverter::decode('UNKO', 'OKNU'));
	}

	public function testEncodeByCallStaticWithMapAndAcceptLong()
	{
		if (!extension_loaded('bcmath') && !extension_loaded('gmp')) {
			$this->markTestSkipped('BcMath extension or GMP extension is required.');
		}
		$this->assertEquals('2lkCB2', RadixConverter::encode('2147483648', RadixConverter::MAP_ALPHANUMERIC_62, true));
		$this->assertEquals('aZl8N0y58M8', RadixConverter::encode('9223372036854775808', RadixConverter::MAP_ALPHANUMERIC_62, true));
	}

	public function testDecodeByCallStaticWithMapAndAcceptLong()
	{
		if (!extension_loaded('bcmath') && !extension_loaded('gmp')) {
			$this->markTestSkipped('BcMath extension or GMP extension is required.');
		}
		$this->assertEquals('2147483648', RadixConverter::decode('2lkCB2', RadixConverter::MAP_ALPHANUMERIC_62, true));
		$this->assertEquals('9223372036854775808', RadixConverter::decode('aZl8N0y58M8', RadixConverter::MAP_ALPHANUMERIC_62, true));
	}

}
