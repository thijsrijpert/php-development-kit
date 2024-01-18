<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
namespace jhp\lang;

use jhp\lang\exception\NumberFormatException;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;

class TIntegerTest extends TestCase
{
    public function testAsStringDecimalPositive() {
        $this->assertEquals("12345", TInteger::asString(12345));
    }

    public function testAsStringDecimalNegative() {
        $this->assertEquals("-12345", TInteger::asString(-12345));
    }

    public function testAsStringTooLargeRadixPositive() {
        $this->assertEquals("12345", TInteger::asString(12345, 37));
    }

    public function testAsStringTooLargeRadixNegative() {
        $this->assertEquals("-12345", TInteger::asString(-12345, 37));
    }

    public function testAsStringTooSmallRadixPositive() {
        $this->assertEquals("12345", TInteger::asString(12345, 1));
    }

    public function testAsStringTooSmallRadixNegative() {
        $this->assertEquals("-12345", TInteger::asString(-12345, 1));
    }

    public function testAsStringHexadecimalPositive() {
        $this->assertEquals("3039", TInteger::asString(12345, 16));
    }

    public function testAsStringHexadecimalNegative() {
        $this->assertEquals("-3039", TInteger::asString(-12345, 16));
    }

    public function testAsStringHexadecimalWithLettersPositive() {
        $this->assertEquals("1f1f", TInteger::asString(7967, 16));
    }

    public function testAsStringHexadecimalWithLettersNegative() {
        $this->assertEquals("-1f1f", TInteger::asString(-7967, 16));
    }

    public function testAsStringBinaryPositive() {
        $this->assertEquals("11000000111001", TInteger::asString(12345, 2));
    }

    public function testAsStringBinaryNegative() {
        $this->assertEquals("-11000000111001", TInteger::asString(-12345, 2));
    }

    public function testToStringDecimalPositive() {
        $this->assertEquals("12345", TInteger::valueOf(12345)->toString());
    }

    public function testToStringDecimalNegative() {
        $this->assertEquals("-12345", TInteger::valueOf(-12345)->toString());
    }

    public function testParseIntPositive() {
        $this->assertEquals(12345, TInteger::parseInt("12345"));
    }

    public function testParseIntPositiveSigned() {
        $this->assertEquals(12345, TInteger::parseInt("+12345"));
    }

    public function testParseIntNegative() {
        $this->assertEquals(-12345, TInteger::parseInt("-12345"));
    }

    public function testParseIntRadixTooSmall() {
        $this->expectException(NumberFormatException::class);
        TInteger::parseInt("12345", 1);
    }

    public function testParseIntRadixTooLarge() {
        $this->expectException(NumberFormatException::class);
        TInteger::parseInt("12345", 37);
    }

    public function testParseIntPositiveRadix16() {
        $this->assertEquals(74565, TInteger::parseInt("12345", 16));
    }

    public function testParseIntPositiveWithSignRadix16() {
        $this->assertEquals(74565, TInteger::parseInt("+12345", 16));
    }

    public function testParseIntNegativeRadix16() {
        $this->assertEquals(-74565, TInteger::parseInt("-12345", 16));
    }

    public function testParseIntPositiveRadix16WithLetters() {
        $this->assertEquals(7967, TInteger::parseInt("1f1f", 16));
    }

    public function testParseIntPositiveWithSignRadix16WithLetters() {
        $this->assertEquals(7967, TInteger::parseInt("+1f1f", 16));
    }

    public function testParseIntNegativeRadix16WithLetters() {
        $this->assertEquals(-7967, TInteger::parseInt("-1f1f", 16));
    }

    public function testParseIntPositiveRadix2() {
        $this->assertEquals(102, TInteger::parseInt("1100110", 2));
    }

    public function testParseIntPositiveWithSignRadix2() {
        $this->assertEquals(102, TInteger::parseInt("+1100110", 2));
    }

    public function testParseIntNegativeWithSignRadix2() {
        $this->assertEquals(-102, TInteger::parseInt("-1100110", 2));
    }

    public function testParseIntLargestPossibleValue() {
        $this->assertEquals(9223372036854775807, TInteger::parseInt("9223372036854775807", 10));
    }

    public function testParseIntNumberFormatExceptionJustNumbers() {
        $this->expectException(NumberFormatException::class);
        TInteger::parseInt("12345", 2);
    }

    public function testParseIntNumberFormatException() {
        $this->expectException(NumberFormatException::class);
        TInteger::parseInt("Kona", 10);
    }

    public function testParseIntKona() {
        $this->assertEquals(411787, TInteger::parseInt("Kona", 27));
    }

    public function testValueOfDecimalPositive() {
        $this->assertEquals(12345, TInteger::valueOf(12345)->intValue());
    }

    public function testValueOfDecimalNegative() {
        $this->assertEquals(-12345, TInteger::valueOf(-12345)->intValue());
    }

    public function testValueOfTooLargeRadix() {
        $this->expectException(NumberFormatException::class);
        TInteger::valueOf(12345, 11);
    }

    public function testValueOfTooSmallRadix()
    {
        $this->expectException(NumberFormatException::class);
        TInteger::valueOf(12345, 9);
    }

    public function testValueOfFromStringDecimalPositive() {
        $this->assertEquals(12345, TInteger::valueOf("12345")->intValue());
    }

    public function testValueOfFromStringDecimalNegative() {
        $this->assertEquals(-12345, TInteger::valueOf("-12345")->intValue());
    }

    public function testValueOfFromStringTooLargeRadix() {
        $this->expectException(NumberFormatException::class);
        TInteger::valueOf("12345", 37);
    }

    public function testValueOfFromStringTooSmallRadix()
    {
        $this->expectException(NumberFormatException::class);
        TInteger::valueOf("12345", 1);
    }

    public function testValueOfFromStringHexaDecimalPositive() {
        $this->assertEquals(7967, TInteger::valueOf("1f1f", 16)->intValue());
    }

    public function testValueOfFromStringHexaDecimalNegative() {
        $this->assertEquals(-7967, TInteger::valueOf("-1f1f", 16)->intValue());
    }

    public function testHashCodeDecimalPositive() {
        $this->assertEquals(12345, TInteger::valueOf(12345)->hashCode());
    }

    public function testHashCodeDecimalNegative() {
        $this->assertEquals(-12345, TInteger::valueOf(-12345)->hashCode());
    }

    public function testEqualsTrue() {
        $input = TInteger::valueOf(12345);
        $this->assertTrue(TInteger::valueOf(12345)->equals($input));
    }

    public function testEqualsFalse() {
        $input = TInteger::valueOf(45678);
        $this->assertFalse(TInteger::valueOf(12345)->equals($input));
    }

    public function testEqualsWrongType() {
        $input = new TestObject();
        $this->assertFalse(TInteger::valueOf(12345)->equals($input));
    }

    public function testEqualsNull() {
        $this->assertFalse(TInteger::valueOf(12345)->equals());
    }

    public function testNumberOfLeadingZerosZero() {
        $this->assertEquals(64, TInteger::numberOfLeadingZeros(0));
    }

    public function testNumberOfLeadingZerosLargestPossibleValue() {
        $this->assertEquals(1, TInteger::numberOfLeadingZeros(TInteger::MAX_VALUE));
    }

    public function testNumberOfLeadingZerosSmallestPossibleValue() {
        $this->assertEquals(0, TInteger::numberOfLeadingZeros(TInteger::MIN_VALUE));
    }

    public function testNumberOfLeadingZerosAllCases() {
        $input = "1";
        for($i = 63; $i > 0; $i--) {
            $parameter = (int) base_convert($input, 2, 10);
            $this->assertEquals($i, TInteger::numberOfLeadingZeros($parameter));
            $input .= "0";
        }
    }

    public function testNumberOfTrailingZerosLargestPossibleValue() {
        $this->assertEquals(0, TInteger::numberOfTrailingZeros(TInteger::MAX_VALUE));
    }

    public function testNumberOfTrailingZerosSmallestPossibleValue() {
        $this->assertEquals(63, TInteger::numberOfTrailingZeros(TInteger::MIN_VALUE));
    }

    public function testNumberOfTrailingZerosAllCases() {
        $input = "1";
        for($i = 0; $i < 63; $i++) {
            $parameter = (int) base_convert($input, 2, 10);
            $this->assertEquals($i, TInteger::numberOfTrailingZeros($parameter));
            $input .= "0";
        }
    }

    public function testBitcountAllCases() {
        $input = "";
        for($i = 0; $i <= 63; $i++) {
            $parameter = (int) base_convert($input, 2, 10);
            $this->assertEquals($i, TInteger::bitCount($parameter));
            $input .= "1";
        }
    }

    public function testBitcountAllCasesHalf() {
        $input = "0";
        for($i = 1; $i <= 63; $i++) {
            $parameter = (int) base_convert($input, 2, 10);
            $this->assertEquals(intdiv($i, 2), TInteger::bitCount($parameter));
            if ($i % 2 === 0) {
                $input .= "0";
            } else {
                $input .= "1";
            }
        }
    }

    public function testBitcountMinusOne() {
        $parameter = -1;
        $this->assertEquals(64, TInteger::bitCount($parameter));
    }

    public function testRotateLeft() {
        $parameter = 0x7800_0000_0000_00F0;
        $result = TInteger::rotateLeft($parameter, 5);
        $this->assertEquals(0x0000_0000_0000_1E0F, $result);
    }

    public function testRotateLeftModulo() {
        $parameter = 0x7800_0000_0000_00F0;
        $result = TInteger::rotateLeft($parameter, 69);
        $this->assertEquals(0x0000_0000_0000_1E0F, $result);
    }

    public function testRotateRight() {
        $parameter = 0x0000_0000_0000_1E0F;
        $result = TInteger::rotateRight($parameter, 5);
        $this->assertEquals(0x7800_0000_0000_00F0, $result);
    }

    public function testSignumLowestValue() {
        $this->assertEquals(-1, TInteger::signum(TInteger::MIN_VALUE));
    }

    public function testSignumMinusOne() {
        $parameter = -1;
        $this->assertEquals(-1, TInteger::signum(-1));
    }

    public function testSignumZero() {
        $this->assertEquals(0, TInteger::signum(0));
    }

    public function testSignumOne() {
        $this->assertEquals(1, TInteger::signum(1));
    }

    public function testSignumHighestValue() {
        $this->assertEquals(1, TInteger::signum(TInteger::MAX_VALUE));
    }

    public function testReverse() {
        $input = 0x0F00_0F00_0F00_F000;
        $result = TInteger::reverse($input);
        $this->assertEquals(0x000F_00F0_00F0_00F0, $result);
    }

    public function testReverseReversed() {
        $input = 0x000F_00F0_00F0_00F0;
        $result = TInteger::reverse($input);
        $this->assertEquals(0x0F00_0F00_0F00_F000, $result);
    }

    public function testReverseBytes() {
        $input = 0x7820_2849_4444_0670;
        $result = TInteger::reverseBytes($input);
        $this->assertEquals(0x7006_4444_4928_2078, $result);
    }

    public function testReverseBytesReversed() {
        $input = 0x7006_4444_4928_2078;
        $result = TInteger::reverseBytes($input);
        $this->assertEquals(0x7820_2849_4444_0670, $result);
    }

    public function testSum() {
        $result = TInteger::sum(1, 1);
        $this->assertEquals(2, $result);
    }

    public function testSumOverflowPositive() {
        $result = TInteger::sum(0x7FFFFFFFFFFFFFFB, 64);
        $this->assertEquals(-9_223_372_036_854_775_749, $result);
    }

    public function testSumOverflowNegative() {
        $result = TInteger::sum(-9_187_343_239_835_811_840, -2_305_843_009_213_693_952);
        $this->assertEquals(6_953_557_824_660_045_824, $result);
    }

    public function testMax() {
        $result = TInteger::max(1, 2);
        $this->assertEquals(2, $result);
    }

    public function testMaxNegative() {
        $result = TInteger::max(-1, -2);
        $this->assertEquals(-1, $result);
    }

    public function testMaxNegativeAndPositive() {
        $result = TInteger::max(-1, 1);
        $this->assertEquals(1, $result);
    }

    public function testMin() {
        $result = TInteger::min(1, 2);
        $this->assertEquals(1, $result);
    }

    public function testMinNegative() {
        $result = TInteger::min(-1, -2);
        $this->assertEquals(-2, $result);
    }

    public function testMinNegativeAndPositive() {
        $result = TInteger::min(-1, 1);
        $this->assertEquals(-1, $result);
    }

    public function testLowestOneBitPositive() {
        $result = TInteger::lowestOneBit(3_485_794_835_784);
        $this->assertEquals(8, $result);
    }

    public function testLowestOneBitPositiveMaxValue() {
        $result = TInteger::lowestOneBit(TInteger::MAX_VALUE);
        $this->assertEquals(1, $result);
    }

    public function testLowestOneBitNegative() {
        $result = TInteger::lowestOneBit(-9_223_368_551_069_384_704);
        $this->assertEquals(16_777_216, $result);
    }

    public function testLowestOneBitNegativeMinValue() {
        $result = TInteger::lowestOneBit(TInteger::MIN_VALUE);
        $this->assertEquals(TInteger::MIN_VALUE, $result);
    }

    public function testHighestOneBitPositive() {
        $result = TInteger::highestOneBit(3_485_794_835_784);
        $this->assertEquals(2_199_023_255_552, $result);
    }

    public function testHighestOneBitPositiveMaxValue() {
        $result = TInteger::highestOneBit(TInteger::MAX_VALUE);
        $this->assertEquals(TInteger::MAX_VALUE, $result);
    }

    public function testHighestOneBitNegative() {
        $result = TInteger::highestOneBit(-9_223_368_551_069_384_704);
        $this->assertEquals(TInteger::MIN_VALUE, $result);
    }

    public function testHighestOneBitNegativeMinValue() {
        $result = TInteger::highestOneBit(TInteger::MIN_VALUE);
        $this->assertEquals(TInteger::MIN_VALUE, $result);
    }
}