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
        $input = "1";
        for($i = 1; $i <= 63; $i++) {
            $parameter = (int) base_convert($input, 2, 10);
            $this->assertEquals($i, TInteger::bitCount($parameter));
            $input .= "1";
        }
    }

}