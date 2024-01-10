<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/**
 * @noinspection PhpEnforceDocCommentInspection
 * @noinspection PhpMissingDocCommentInspection
 */

namespace jhp\lang;

use jhp\lang\exception\IllegalArgumentException;
use jhp\testhelper\TestObject;
use PHPUnit\Framework\TestCase;

class TBooleanTest extends TestCase
{
    public function testParseBooleanTrue()
    {
        $this->assertTrue(TBoolean::parseBoolean("true"));
    }

    public function testParseBooleanFalse()
    {
        $this->assertFalse(TBoolean::parseBoolean("false"));
    }

    public function testParseBooleanTrueCapitalized()
    {
        $this->assertTrue(TBoolean::parseBoolean("TRUE"));
    }

    public function testParseBooleanFalseCapitalized()
    {
        $this->assertFalse(TBoolean::parseBoolean("FALSE"));
    }

    public function testBooleanValueTrue()
    {
        $this->assertTrue(TBoolean::valueOf(true)->booleanValue());
    }

    public function testBooleanValueFalse()
    {
        $this->assertFalse(TBoolean::valueOf(false)->booleanValue());
    }

    public function testBooleanValueTrueString()
    {
        $this->assertTrue(TBoolean::valueOf("true")->booleanValue());
    }

    public function testBooleanValueFalseString()
    {
        $this->assertFalse(TBoolean::valueOf("false")->booleanValue());
    }

    public function testBooleanValueTrueStringCapitalized()
    {
        $this->assertTrue(TBoolean::valueOf("TRUE")->booleanValue());
    }

    public function testBooleanValueFalseStringCapitalized()
    {
        $this->assertFalse(TBoolean::valueOf("FALSE")->booleanValue());
    }

    public function testToStringTrue()
    {
        $this->assertEquals("true", TBoolean::valueOf(true)->toString());
    }

    public function testToStringFalse()
    {
        $this->assertEquals("false", TBoolean::valueOf(false)->toString());
    }

    public function testAsStringTrue()
    {
        $this->assertEquals("true", TBoolean::asString(true));
    }

    public function testAsStringFalse()
    {
        $this->assertEquals("false", TBoolean::asString(false));
    }

    public function testHashCodeTrue()
    {
        $this->assertEquals(1231, TBoolean::valueOf(true)->hashCode());
    }

    public function testHashCodeFalse()
    {
        $this->assertEquals(1237, TBoolean::valueOf(false)->hashCode());
    }

    public function testAsHashCodeTrue()
    {
        $this->assertEquals(1231, TBoolean::asHashCode(true));
    }

    public function testAsHashCodeFalse()
    {
        $this->assertEquals(1237, TBoolean::asHashCode(false));
    }

    public function testEqualsTrue()
    {
        $input = TBoolean::valueOf(true);

        $boolean = TBoolean::valueOf(true);

        $result = $boolean->equals($input);

        $this->assertTrue($result);
    }

    public function testEqualsFalse()
    {
        $input = TBoolean::valueOf(true);

        $boolean = TBoolean::valueOf(false);

        $result = $boolean->equals($input);

        $this->assertFalse($result);
    }

    public function testEqualsInvalidType()
    {
        $input = new TestObject();

        $boolean = TBoolean::valueOf(false);

        $result = $boolean->equals($input);

        $this->assertFalse($result);
    }

    public function testEqualsNull()
    {
        $input = null;

        $boolean = TBoolean::valueOf(false);

        $result = $boolean->equals($input);

        $this->assertFalse($result);
    }

    public function testCompareToEqual()
    {
        $input = TBoolean::valueOf(false);

        $boolean = TBoolean::valueOf(false);

        $result = $boolean->compareTo($input);

        $this->assertEquals(0, $result);
    }

    public function testCompareToLarger()
    {
        $input = TBoolean::valueOf(false);

        $boolean = TBoolean::valueOf(true);

        $result = $boolean->compareTo($input);

        $this->assertEquals(1, $result);
    }

    public function testCompareToSmaller()
    {
        $input = TBoolean::valueOf(true);

        $boolean = TBoolean::valueOf(false);

        $result = $boolean->compareTo($input);

        $this->assertEquals(-1, $result);
    }

    public function testCompareToInvalidType()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = new TestObject();

        $boolean = TBoolean::valueOf(false);

        $boolean->compareTo($input);
    }

    public function testCompareEqual()
    {
        $result = TBoolean::compare(true, true);

        $this->assertEquals(0, $result);
    }

    public function testCompareLarger()
    {
        $result = TBoolean::compare(true, false);

        $this->assertEquals(1, $result);
    }

    public function testCompareSmaller()
    {
        $result = TBoolean::compare(false, true);

        $this->assertEquals(-1, $result);
    }

    public function testLogicalAndBothTrue()
    {
        $this->assertTrue(TBoolean::logicalAnd(true, true));
    }

    public function testLogicalAndAFalse()
    {
        $this->assertFalse(TBoolean::logicalAnd(false, true));
    }

    public function testLogicalAndBFalse()
    {
        $this->assertFalse(TBoolean::logicalAnd(true, false));
    }

    public function testLogicalAndBothFalse()
    {
        $this->assertFalse(TBoolean::logicalAnd(false, false));
    }

    public function testLogicalOrBothTrue()
    {
        $this->assertTrue(TBoolean::logicalOr(true, true));
    }

    public function testLogicalOrAFalse()
    {
        $this->assertTrue(TBoolean::logicalOr(false, true));
    }

    public function testLogicalOrBFalse()
    {
        $this->assertTrue(TBoolean::logicalOr(true, false));
    }

    public function testLogicalOrBothFalse()
    {
        $this->assertFalse(TBoolean::logicalOr(false, false));
    }

    public function testLogicalXorBothTrue()
    {
        $this->assertFalse(TBoolean::logicalXor(true, true));
    }

    public function testLogicalXorAFalse()
    {
        $this->assertTrue(TBoolean::logicalXor(false, true));
    }

    public function testLogicalXorBFalse()
    {
        $this->assertTrue(TBoolean::logicalXor(true, false));
    }

    public function testLogicalXorBothFalse()
    {
        $this->assertFalse(TBoolean::logicalXor(false, false));
    }
}