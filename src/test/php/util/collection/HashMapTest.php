<?php

namespace jhp\util\collection;

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\TClass;
use jhp\lang\TObject;
use jhp\testhelper\HashableTestObject;
use jhp\testhelper\HashableTestObjectChild;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\TestObject;
use jhp\testhelper\TestObjectChild;
use jhp\util\function\BiConsumer;
use jhp\util\function\BiFunction;
use PHPUnit\Framework\TestCase;

class HashMapTest extends TestCase
{

    private readonly THashMap $data;

    private readonly THashMap $empty;
    public function setUp(): void
    {
        $this->data = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TestObject::class),
            THashMapEntry::of((new HashableTestObject())->setValue(1), (new TestObject())->setValue("One")),
            THashMapEntry::of((new HashableTestObject())->setValue(2), (new TestObject())->setValue("Two")),
            THashMapEntry::of((new HashableTestObject())->setValue(3), (new TestObject())->setValue("Three")),
            THashMapEntry::of((new HashableTestObject())->setValue(4), (new TestObject())->setValue("Four")),
            THashMapEntry::of((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Five")),
        );

        $this->empty = new THashMap(TClass::from(HashableTestObject::class), TClass::from(TestObject::class));
    }

    public function testGetEmpty() {
        $result = $this->empty->get((new HashableTestObject())->setValue(5));
        $this->assertNull($result);
    }

    public function testGetNotSet() {
        $result = $this->empty->get((new HashableTestObject())->setValue(6));
        $this->assertNull($result);
    }

    public function testGet() {
        $result = $this->data->get((new HashableTestObject())->setValue(5));
        if ($result instanceof TestObject) {
            $this->assertEquals("Five", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetWrongType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->get((new TestObject())->setValue("Test"));
    }

    public function testSize() {
        $this->assertEquals(5, $this->data->size());
    }

    public function testSizeEmpty() {
        $this->assertEquals(0, $this->empty->size());
    }

    public function testEmpty() {
        $this->assertFalse($this->data->isEmpty());
    }

    public function testEmptyEmpty() {
        $this->assertTrue($this->empty->isEmpty());
    }

    public function testContainsKey() {
        $this->assertTrue($this->data->containsKey((new HashableTestObject())->setValue(5)));
    }

    public function testContainsKeyNotFound() {
        $this->assertFalse($this->data->containsKey((new HashableTestObject())->setValue(6)));
    }

    public function testContainsKeyEmpty() {
        $this->assertFalse($this->empty->containsKey((new HashableTestObject())->setValue(6)));
    }

    public function testContainsKeyInvalidDateType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->containsKey((new TestObject())->setValue("Test"));
    }

    public function testContainsKeyParentType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->containsKey(new TObject());
    }

    public function testContainsKeyChildDateTypeInvalidDateType() {
        $this->assertFalse($this->data->containsKey((new HashableTestObjectChild())->setValue(1)));
    }

    public function testContainsValue() {
        $this->assertTrue($this->data->containsValue((new TestObject())->setValue("Five")));
    }

    public function testContainsValueNotFound() {
        $this->assertFalse($this->data->containsValue((new TestObject())->setValue("Six")));
    }

    public function testContainsValueEmpty() {
        $this->assertFalse($this->empty->containsValue((new TestObject())->setValue("Six")));
    }

    public function testContainsValueInvalidDateType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->containsValue((new NotTestObject())->setValue("Test"));
    }

    public function testContainsValueParentType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->containsValue(new TObject());
    }

    public function testContainsValueChildDateTypeInvalidDateType() {
        $this->assertFalse($this->data->containsValue((new TestObjectChild())->setValue("Test")));
    }

    public function testPutInsert() {
        $result = $this->data->put((new HashableTestObject())->setValue(6), (new TestObject())->setValue("Six"));
        $item = $this->data->get((new HashableTestObject())->setValue(6));
        $this->assertNull($result);
        $this->assertEquals(6, $this->data->size());
        $this->assertNotNull($item);
        if (!($item instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("Six", $item->getValue());
    }

    public function testPutReplace() {
        $result = $this->data->put((new HashableTestObject())->setValue(5), (new TestObject())->setValue("New value"));
        if (!($result instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("Five", $result->getValue());
        $this->assertEquals(5, $this->data->size());
    }

    public function testPutWithChildObjectKey() {
        $result = $this->data->put((new HashableTestObjectChild())->setValue(5), (new TestObject())->setValue("New value"));
        $item = $this->data->get((new HashableTestObjectChild())->setValue(5));
        $this->assertNull($result);
        $this->assertEquals(6, $this->data->size());
        $this->assertNotNull($item);
        if (!($item instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("New value", $item->getValue());
    }

    public function testPutWithChildObjectValue() {
        $this->data->put((new HashableTestObject())->setValue(5), (new TestObjectChild())->setValue("New value"));
        $item = $this->data->get((new HashableTestObject())->setValue(5));
        $this->assertEquals(5, $this->data->size());
        $this->assertNotNull($item);
        if (!($item instanceof TestObject)) {
            $this->fail();
        }
        $this->assertEquals("New value", $item->getValue());
    }

    public function testPutInvalidKey() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->put(new TObject(), (new TestObject())->setValue("New value"));
    }

    public function testPutInvalidValue() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->put(new HashableTestObject(), new TObject());
    }

    public function testPutAllInsert()
    {
        $collection = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TestObject::class),
            THashMapEntry::of((new HashableTestObject())->setValue(6), (new TestObject())->setValue("Six")),
            THashMapEntry::of((new HashableTestObject())->setValue(7), (new TestObject())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObject())->setValue(8), (new TestObject())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObject())->setValue(9), (new TestObject())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObject())->setValue(10), (new TestObject())->setValue("Ten")),
        );

        $this->data->putAll($collection);

        $this->assertEquals(10, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(8));
        if ($result instanceof TestObject) {
            $this->assertEquals("Eight", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testPutAllReplace()
    {
        $collection = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TestObject::class),
            THashMapEntry::of((new HashableTestObject())->setValue(1), (new TestObject())->setValue("Six")),
            THashMapEntry::of((new HashableTestObject())->setValue(2), (new TestObject())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObject())->setValue(3), (new TestObject())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObject())->setValue(4), (new TestObject())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Ten")),
        );

        $this->data->putAll($collection);

        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Eight", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testPutAllInvalidKey()
    {
        $this->expectException(IllegalArgumentException::class);
        $collection = THashMap::ofEntries(TClass::from(TObject::class), TClass::from(TestObject::class),
            THashMapEntry::of((new HashableTestObject())->setValue(1), (new TestObject())->setValue("Six")),
            THashMapEntry::of((new HashableTestObject())->setValue(2), (new TestObject())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObject())->setValue(3), (new TestObject())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObject())->setValue(4), (new TestObject())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Ten")),
        );

        $this->data->putAll($collection);
    }

    public function testPutAllInvalidValue()
    {
        $this->expectException(IllegalArgumentException::class);
        $collection = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TObject::class),
            THashMapEntry::of((new HashableTestObject())->setValue(1), (new TestObject())->setValue("Six")),
            THashMapEntry::of((new HashableTestObject())->setValue(2), (new TestObject())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObject())->setValue(3), (new TestObject())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObject())->setValue(4), (new TestObject())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Ten")),
        );

        $this->data->putAll($collection);
    }

    public function testPutAllReplaceChildObjectKey()
    {
        $collection = THashMap::ofEntries(TClass::from(HashableTestObjectChild::class), TClass::from(TestObject::class),
            THashMapEntry::of((new HashableTestObjectChild())->setValue(1), (new TestObject())->setValue("Six")),
            THashMapEntry::of((new HashableTestObjectChild())->setValue(2), (new TestObject())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObjectChild())->setValue(3), (new TestObject())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObjectChild())->setValue(4), (new TestObject())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObjectChild())->setValue(5), (new TestObject())->setValue("Ten")),
        );

        $this->data->putAll($collection);

        $this->assertEquals(10, $this->data->size());
        $result = $this->data->get((new HashableTestObjectChild())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Eight", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testPutAllReplaceChildObjectValue()
    {
        $collection = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TestObjectChild::class),
            THashMapEntry::of((new HashableTestObject())->setValue(1), (new TestObjectChild())->setValue("Six")),
            THashMapEntry::of((new HashableTestObject())->setValue(2), (new TestObjectChild())->setValue("Seven")),
            THashMapEntry::of((new HashableTestObject())->setValue(3), (new TestObjectChild())->setValue("Eight")),
            THashMapEntry::of((new HashableTestObject())->setValue(4), (new TestObjectChild())->setValue("Nine")),
            THashMapEntry::of((new HashableTestObject())->setValue(5), (new TestObjectChild())->setValue("Ten")),
        );

        $this->data->putAll($collection);

        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Eight", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testPutAllEmpty()
    {
        $collection = THashMap::ofEntries(TClass::from(HashableTestObject::class), TClass::from(TestObject::class),
        );

        $this->data->putAll($collection);

        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Three", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testClear() {
        $this->data->clear();

        $this->assertTrue($this->data->isEmpty());
    }

    public function testGetDefaultEmpty() {
        $result = $this->empty->getOrDefault((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Default"));
        if ($result instanceof TestObject) {
            $this->assertEquals("Default", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetDefaultNotSet() {
        $result = $this->empty->getOrDefault((new HashableTestObject())->setValue(6), (new TestObject())->setValue("Default"));
        if ($result instanceof TestObject) {
            $this->assertEquals("Default", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetDefault() {
        $result = $this->data->getOrDefault((new HashableTestObject())->setValue(5), new TestObject());
        if ($result instanceof TestObject) {
            $this->assertEquals("Five", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetDefaultNotSetChildObject() {
        $result = $this->empty->getOrDefault((new HashableTestObject())->setValue(6), (new TestObjectChild())->setValue("Default"));
        if ($result instanceof TestObject) {
            $this->assertEquals("Default", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetDefaultNotSetKeyChildObject() {
        $result = $this->empty->getOrDefault((new HashableTestObjectChild())->setValue(6), (new TestObject())->setValue("Default"));
        if ($result instanceof TestObject) {
            $this->assertEquals("Default", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testGetDefaultWrongType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->getOrDefault((new TestObject())->setValue("Test"), new TestObject());
    }
    public function testRemoveNotSet() {
        $result = $this->data->remove((new HashableTestObject())->setValue(6), (new TestObject())->setValue("Default"));
        $this->assertNull($result);
        $this->assertEquals(5, $this->data->size());
    }

    public function testRemoveSet() {
        $result = $this->data->remove((new HashableTestObject())->setValue(5));
        $this->assertNotNull($result);
        $this->assertEquals(4, $this->data->size());
        if ($result instanceof TestObject) {
            $this->assertEquals("Five", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testRemoveSetWithRightValue() {
        $result = $this->data->remove((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Five"));
        $this->assertNotNull($result);
        $this->assertEquals(4, $this->data->size());
        if ($result instanceof TestObject) {
            $this->assertEquals("Five", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testRemoveSetButWithDifferentValue() {
        $result = $this->data->remove((new HashableTestObject())->setValue(5), (new TestObject())->setValue("Wrong Value"));
        $this->assertNull($result);
        $this->assertEquals(5, $this->data->size());
    }

    public function testRemoveWrongKeyType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->remove(new TObject(), (new TestObject())->setValue("Wrong Value"));
    }

    public function testRemoveWrongValueType() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->remove(new HashableTestObject(), new TObject());
    }

    public function testForeach() {
        $this->data->forEach(BiConsumer::of(fn(HashableTestObject $key, TestObject $value) => $value->setValue($value->getValue() . "Test")));
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(1));
        if ($result instanceof TestObject) {
            $this->assertEquals("OneTest", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceAll() {
        $this->data->replaceAll(BiFunction::of(fn(HashableTestObject $key, TestObject $value) => (new TestObject())->setValue($value->getValue() . "Test")));
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(1));
        if ($result instanceof TestObject) {
            $this->assertEquals("OneTest", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplace() {
        $old = $this->data->replace((new HashableTestObject())->setValue(3), (new TestObject())->setValue("Test"));
        if ($old instanceof TestObject) {
            $this->assertEquals("Three", $old->getValue());
        } else {
            $this->fail();
        }
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Test", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceWithMatchingValue() {
        $old = $this->data->replace((new HashableTestObject())->setValue(3),(new TestObject())->setValue("Three"), (new TestObject())->setValue("Test"));
        if ($old instanceof TestObject) {
            $this->assertEquals("Three", $old->getValue());
        } else {
            $this->fail();
        }
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Test", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceWithNotMatchingValue() {
        $old = $this->data->replace((new HashableTestObject())->setValue(3),(new TestObject())->setValue("Wrong"), (new TestObject())->setValue("Test"));
        $this->assertNull($old);
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Three", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceWithNotMatchingKeyChildKey() {
        $old = $this->data->replace((new HashableTestObjectChild())->setValue(3),(new TestObject())->setValue("Wrong"), (new TestObject())->setValue("Test"));
        $this->assertNull($old);
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Three", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceWithNotMatchingValueChildValue() {
        $old = $this->data->replace((new HashableTestObject())->setValue(3),(new TestObjectChild())->setValue("Three"), (new TestObject())->setValue("Test"));
        $this->assertNull($old);
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Three", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceWithMatchingValueChildValue() {
        $this->data->replace((new HashableTestObject())->setValue(3),(new TestObject())->setValue("Three"), (new TestObjectChild())->setValue("Test"));
        $this->assertEquals(5, $this->data->size());
        $result = $this->data->get((new HashableTestObject())->setValue(3));
        if ($result instanceof TestObject) {
            $this->assertEquals("Test", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testReplaceIllegalKey() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->replace(new TObject(),(new TestObject())->setValue("Three"), (new TestObjectChild())->setValue("Test"));
    }

    public function testReplaceIllegalValue() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->replace((new HashableTestObject())->setValue(3),(new TestObject())->setValue("Three"), new TObject());
    }

    public function testReplaceIllegalCheckValue() {
        $this->expectException(IllegalArgumentException::class);
        $this->data->replace((new HashableTestObject())->setValue(3),new TObject(), (new TestObject())->setValue("Test"));
    }

    public function testOffsetExistsInvalid() {
        $this->expectException(IllegalArgumentException::class);
        isset($this->data[0]);
    }
    public function testOffsetExistsTrue() {
        $this->assertTrue(isset($this->data[(new HashableTestObject())->setValue(3)]));
    }

    public function testOffsetExistsFalse() {
        $this->assertFalse(isset($this->data[(new HashableTestObject())->setValue(6)]));
    }

    public function testOffsetGetInvalid() {
        $this->expectException(IllegalArgumentException::class);
        $this->data[3];
    }

    public function testOffsetGetExists() {
        $result = $this->data[(new HashableTestObject())->setValue(3)];
        if ($result instanceof TestObject) {
            $this->assertEquals("Three", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testOffsetGetDoesNotExist() {
        $result = $this->data[(new HashableTestObject())->setValue(6)];
        $this->assertNull($result);
    }

    public function testOffsetSetNullKey() {
        $this->expectException(IllegalArgumentException::class);
        $this->data[] = (new TestObject())->setValue("Test");
    }

    public function testOffsetSetInvalidKey() {
        $this->expectException(IllegalArgumentException::class);
        $this->data[0] = (new TestObject())->setValue("Test");
    }

    public function testOffsetSetReplace() {
        $this->data[(new HashableTestObject())->setValue(3)] = (new TestObject())->setValue("Test");
        $this->assertEquals(5, $this->data->size());
        $result = $this->data[(new HashableTestObject())->setValue(3)];
        if ($result instanceof TestObject) {
            $this->assertEquals("Test", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testOffsetSetInsert() {
        $this->data[(new HashableTestObject())->setValue(6)] = (new TestObject())->setValue("Test");
        $this->assertEquals(6, $this->data->size());
        $result = $this->data[(new HashableTestObject())->setValue(6)];
        if ($result instanceof TestObject) {
            $this->assertEquals("Test", $result->getValue());
        } else {
            $this->fail();
        }
    }

    public function testUnsetInvalid() {
        $this->expectException(IllegalArgumentException::class);
        unset($this->data[0]);
    }

    public function testUnsetTrue() {
        unset($this->data[(new HashableTestObject())->setValue(3)]);
        $this->assertEquals(4, $this->data->size());
    }
}