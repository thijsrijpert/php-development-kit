<?php
/*
 * Copyright (c) 2024 Thijs Rijpert
 */
/**
 * @noinspection PhpEnforceDocCommentInspection
 * @noinspection PhpMissingDocCommentInspection
 */

namespace jhp\util\collection;

use jhp\lang\exception\IllegalArgumentException;
use jhp\lang\exception\IndexOutOfBoundsException;
use jhp\lang\TClass;
use jhp\testhelper\NotTestObject;
use jhp\testhelper\SortableTestObject;
use jhp\testhelper\TestCollection;
use jhp\testhelper\TestObject;
use jhp\testhelper\TestObjectChild;
use jhp\util\function\Consumer;
use jhp\util\function\Predicate;
use jhp\util\function\UnaryOperator;
use PHPUnit\Framework\TestCase;
use TypeError;

use function PHPUnit\Framework\assertEquals;

class ArrayListTest extends TestCase
{
    private TestCollection $data;

    public function setUp(): void
    {
        $this->data = new TestCollection(
            TClass::from(TestObject::class), [
            (new TestObject())->setValue("One"),
            (new TestObject())->setValue("Two"),
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Five"),
        ]);
    }

    public function testInitInvalidDataType()
    {
        $this->expectException(IllegalArgumentException::class);
        $this->data = new TestCollection(
            TClass::from(TestObject::class), [
            (new TestObject())->setValue("One"),
            (new TestObject())->setValue("Two"),
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Four"),
            (new NotTestObject())->setValue("Five"),
        ]
        );
        new ArrayList(TClass::from(TestObject::class), $this->data);
    }

    public function testSize()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->size();

        $this->assertEquals(5, $result);
    }

    public function testIsEmptyFilled()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->isEmpty();

        $this->assertFalse($result);
    }

    public function testIsEmptyEmpty()
    {
        $list = new ArrayList(TClass::from(TestObject::class));

        $result = $list->isEmpty();

        $this->assertTrue($result);
    }

    public function testContainsIllegalType()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->contains((new NotTestObject())->setValue("Three"));

        $this->assertFalse($result);
    }

    public function testContainsTrue()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->contains((new TestObject())->setValue("Three"));

        $this->assertTrue($result);
    }

    public function testContainsFalse()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->contains((new TestObject())->setValue("Zero"));

        $this->assertFalse($result);
    }

    public function testContainsNoEqualsImplementationFalse()
    {
        $data = new TestCollection(
            TClass::from(NotTestObject::class),
            [(new NotTestObject())->setValue("One")]
        );

        $list = new ArrayList(TClass::from(NotTestObject::class), $data);

        $result = $list->contains((new NotTestObject())->setValue("One"));

        $this->assertFalse($result);
    }

    public function testContainsNoEqualsImplementationTrue()
    {
        $item = (new NotTestObject())->setValue("One");
        $data = new TestCollection(TClass::from(NotTestObject::class), [$item]);
        $list = new ArrayList(TClass::from(NotTestObject::class), $data);

        $result = $list->contains($item);

        $this->assertTrue($result);
    }

    public function testAddAndGet()
    {
        $input = (new TestObject())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->add($input);
        $addedValue = $list->get(5);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    public function testAddAndGetSubclass()
    {
        $input = (new TestObjectChild())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->add($input);
        $addedValue = $list->get(5);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    public function testAddInvalidObjectOrder()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = (new NotTestObject())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        // Argument 1 should be an index
        $list->add($input, $input);
    }

    public function testAddInvalidObjectOrderWithIndex()
    {
        $this->expectException(IllegalArgumentException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        // Argument 2 should be an object
        $list->add(1);
    }


    public function testAddInvalidType()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = (new NotTestObject())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->add($input);
    }

    public function testAddAndGetIndexed()
    {
        $input = (new TestObject())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->add(3, $input);
        $addedValue = $list->get(3);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    public function testAddAndGetIndexedOutOfBounds()
    {
        $this->expectException(IndexOutOfBoundsException::class);
        $input = (new TestObject())->setValue("FarAwayIndex");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->add(6, $input);
    }

    public function testAddAndGetIndexedJustInsideOfBounds()
    {
        $input = (new TestObject())->setValue("JustCloseEnoughIndex");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->add(5, $input);
        $addedValue = $list->get(5);

        $this->assertTrue($result);
        $this->assertEquals($input, $addedValue);
    }

    public function testAddAndGetIndexedNegative()
    {
        $this->expectException(IndexOutOfBoundsException::class);
        $input = (new TestObject())->setValue("Negative");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->add(-1, $input);
    }

    public function testAddInvalidIndexed()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = (new NotTestObject())->setValue("Six");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->add(3, $input);
    }

    public function testContainsAllIllegalType()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->containsAll(
            new ArrayList(
                TClass::from(NotTestObject::class), new TestCollection(
                    TClass::from(NotTestObject::class), [
                        (new NotTestObject())->setValue("Three"),
                        (new NotTestObject())->setValue("Two")
                    ]
                )
            )
        );

        $this->assertFalse($result);
    }

    public function testContainsAllTrue()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->containsAll(
            new ArrayList(
                TClass::from(TestObject::class), new TestCollection(
                    TClass::from(TestObject::class), [
                        (new TestObject())->setValue("Three"),
                        (new TestObject())->setValue("Two")
                    ]
                )
            )
        );

        $this->assertTrue($result);
    }

    public function testContainsAllFalse()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->containsAll(
            new ArrayList(
                TClass::from(TestObject::class), new TestCollection(
                    TClass::from(TestObject::class), [
                        (new TestObject())->setValue("Zero"),
                        (new TestObject())->setValue("Two")
                    ]
                )
            )
        );

        $this->assertFalse($result);
    }

    public function testToArray()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $results = $list->toArray();

        assertEquals($this->data->toArray(), $results);
    }

    public function testToArrayByReference()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $reference = [];
        $results = $list->toArray($reference);

        assertEquals($this->data->toArray(), $results);
        assertEquals($this->data->toArray(), $reference);
    }

    public function testToArrayByReferenceWithItems()
    {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $reference = [];
        $reference[0] = (new TestObject())->setValue("ToBeOverwritten");
        $reference[5] = (new TestObject())->setValue("Six");
        $reference[6] = (new TestObject())->setValue("Seven");

        $results = $list->toArray($reference);

        $expected = $this->data->toArray();
        $expected[5] = null;
        $expected[6] = (new TestObject())->setValue("Seven");


        assertEquals(7, count($results));
        assertEquals($expected, $results);
        assertEquals(7, count($reference));
        assertEquals($expected, $reference);
    }

    public function testAddAllAndGet()
    {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new TestObject())->setValue("Six"),
                (new TestObject())->setValue("Seven"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->addAll($input);
        $addedValue = $list->get(5);
        $secondAddedValue = $list->get(6);

        $this->assertTrue($result);
        $this->assertEquals("Six", $addedValue->getValue());
        $this->assertEquals("Seven", $secondAddedValue->getValue());
    }

    public function testAddAllInvalidObjectOrder()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new TestObject())->setValue("Six"),
                (new TestObject())->setValue("Seven"),
            ]
            )
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        // Argument 1 should be an index
        $list->addAll($input, $input);
    }

    public function testAddAllInvalidObjectOrderWithIndex()
    {
        $this->expectException(IllegalArgumentException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        // Argument 2 should be an object
        $list->addAll(1);
    }


    public function testAddAllInvalidType()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = new ArrayList(
            TClass::from(NotTestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new NotTestObject())->setValue("Six"),
                (new TestObject())->setValue("Seven"),
            ]
            )
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->addAll($input);
    }

    public function testAddAllAndGetIndexed()
    {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new TestObject())->setValue("Six"),
                (new TestObject())->setValue("Seven"),
            ]
            )
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->addAll(3, $input);
        $addedValue = $list->get(3);
        $secondAddedValue = $list->get(4);
        $originalThirdValue = $list->get(5);

        $this->assertTrue($result);
        $this->assertEquals("Six", $addedValue->getValue());
        $this->assertEquals("Seven", $secondAddedValue->getValue());
        $this->assertEquals("Four", $originalThirdValue->getValue());
    }

    public function testAddAllAndGetIndexedOutOfBounds()
    {
        $this->expectException(IndexOutOfBoundsException::class);
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("FarAwayIndex"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->addAll(6, $input);
    }

    public function testAddAllAndGetIndexedJustInsideOfBounds()
    {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("JustCloseEnoughIndex"),
                (new TestObject())->setValue("JustCloseEnoughIndex2"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->addAll(5, $input);
        $addedValue = $list->get(5);
        $secondAddedValue = $list->get(6);


        $this->assertTrue($result);
        $this->assertEquals("JustCloseEnoughIndex", $addedValue->getValue());
        $this->assertEquals("JustCloseEnoughIndex2", $secondAddedValue->getValue());
    }

    public function testAddAllAndGetIndexedEmptyList()
    {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->addAll(5, $input);
        $this->assertFalse($result);
    }

    public function testAddAllAndGetIndexedNegative()
    {
        $this->expectException(IndexOutOfBoundsException::class);
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new TestObject())->setValue("Negative"),
            ]
            )
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->addAll(-1, $input);
    }

    public function testAddAllInvalidIndexed()
    {
        $this->expectException(IllegalArgumentException::class);
        $input = new ArrayList(
            TClass::from(NotTestObject::class),
            new TestCollection(
                TClass::from(TestObject::class), [
                (new NotTestObject())->setValue("Negative"),
            ]
            )
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->addAll(3, $input);
    }

    public function testRemoveAll() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("One"),
                (new TestObject())->setValue("Two"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->removeAll($input);

        $this->assertTrue($result);
        $this->assertEquals(3, $list->size());
        $this->assertEquals("Three", $list->get(0)->getValue());
        $this->assertEquals("Four", $list->get(1)->getValue());
        $this->assertEquals("Five", $list->get(2)->getValue());
    }

    public function testRemoveAllNoExistent() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("Fake"),
                (new TestObject())->setValue("Two"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->removeAll($input);

        $this->assertTrue($result);
        $this->assertEquals(4, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Three", $list->get(1)->getValue());
        $this->assertEquals("Four", $list->get(2)->getValue());
        $this->assertEquals("Five", $list->get(3)->getValue());
    }

    public function testRemoveAllNoChange() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("Fake"),
                (new TestObject())->setValue("FakeTwo"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->removeAll($input);

        $this->assertFalse($result);
        $this->assertEquals(5, $list->size());
    }

    public function testRetainAll() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("One"),
                (new TestObject())->setValue("Two"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->retainAll($input);

        $this->assertTrue($result);
        $this->assertEquals(2, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Two", $list->get(1)->getValue());
    }

    public function testRetainAllWithNonExistent() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("Fake"),
                (new TestObject())->setValue("Two"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->retainAll($input);

        $this->assertTrue($result);
        $this->assertEquals(1, $list->size());
        $this->assertEquals("Two", $list->get(0)->getValue());
    }

    public function testRetainAllWithNoMatchingItems() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("Fake"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->retainAll($input);

        $this->assertTrue($result);
        $this->assertEquals(0, $list->size());
    }

    public function testRetainAllWithAllMatchingItems() {
        $input = new ArrayList(
            TClass::from(TestObject::class),
            new TestCollection(
                TClass::from(TestObject::class),[
                (new TestObject())->setValue("One"),
                (new TestObject())->setValue("Two"),
                (new TestObject())->setValue("Three"),
                (new TestObject())->setValue("Four"),
                (new TestObject())->setValue("Five"),
            ])
        );
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->retainAll($input);

        $this->assertFalse($result);
        $this->assertEquals(5, $list->size());
    }

    public function testReplaceAll() {
        $input = UnaryOperator::of(fn(TestObject $value) => $value->setValue($value->getValue() . "Test"));
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->replaceAll($input);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("OneTest", $list->get(0)->getValue());
        $this->assertEquals("TwoTest", $list->get(1)->getValue());
        $this->assertEquals("ThreeTest", $list->get(2)->getValue());
        $this->assertEquals("FourTest", $list->get(3)->getValue());
        $this->assertEquals("FiveTest", $list->get(4)->getValue());
    }

    public function testSort() {
        $input = Comparator::of(fn(TestObject $a, TestObject $b) => ($a->getValue() <=> $b->getValue()));
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->sort($input);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("Five", $list->get(0)->getValue());
        $this->assertEquals("Four", $list->get(1)->getValue());
        $this->assertEquals("One", $list->get(2)->getValue());
        $this->assertEquals("Three", $list->get(3)->getValue());
        $this->assertEquals("Two", $list->get(4)->getValue());
    }

    public function testSortInvalidComparator() {
        $this->expectException(TypeError::class);
        $input = Comparator::of(fn(TestObject $a, TestObject $b) => "Test");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->sort($input);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("Five", $list->get(0)->getValue());
        $this->assertEquals("Four", $list->get(1)->getValue());
        $this->assertEquals("One", $list->get(2)->getValue());
        $this->assertEquals("Three", $list->get(3)->getValue());
        $this->assertEquals("Two", $list->get(4)->getValue());
    }

    public function testSortWithoutComparatorAndComparable() {
        $this->expectException(IllegalArgumentException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->sort();
    }

    public function testSortWithoutComparator() {
        $data = new TestCollection(
            TClass::from(TestObject::class), [
            (new SortableTestObject())->setValue("One"),
            (new SortableTestObject())->setValue("Two"),
            (new SortableTestObject())->setValue("Three"),
            (new SortableTestObject())->setValue("Four"),
            (new SortableTestObject())->setValue("Five"),
        ]);
        $list = new ArrayList(TClass::from(TestObject::class), $data);

        $list->sort();

        $this->assertEquals(5, $list->size());
        $this->assertEquals("Five", $list->get(0)->getValue());
        $this->assertEquals("Four", $list->get(1)->getValue());
        $this->assertEquals("One", $list->get(2)->getValue());
        $this->assertEquals("Three", $list->get(3)->getValue());
        $this->assertEquals("Two", $list->get(4)->getValue());
    }

    public function testSortWithoutComparatorAndOneIllegalItem() {
        $this->expectException(IllegalArgumentException::class);

        $data = new TestCollection(
            TClass::from(TestObject::class), [
            (new SortableTestObject())->setValue("One"),
            (new SortableTestObject())->setValue("Two"),
            (new SortableTestObject())->setValue("Three"),
            (new SortableTestObject())->setValue("Four"),
            (new TestObject())->setValue("Five"),
        ]);
        $list = new ArrayList(TClass::from(TestObject::class), $data);

        $list->sort();
    }

    public function testClear() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->clear();

        $this->assertEquals(0, $list->size());
    }

    public function testSet() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->set(2, (new TestObject())->setValue("New Value"));

        if (!($result instanceof TestObject)) {
            $this->fail();
        }

        $this->assertEquals(5, $list->size());
        $this->assertEquals("New Value", $list->get(2)->getValue());
        $this->assertEquals("Three", $result->getValue());
    }

    public function testSetInvalidType() {
        $this->expectException(IllegalArgumentException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->set(2, (new NotTestObject())->setValue("New Value"));
    }

    public function testSetNegative() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->set(-1, (new NotTestObject())->setValue("New Value"));
    }

    public function testSetTooLarge() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->set(5, (new NotTestObject())->setValue("New Value"));
    }

    public function testRemove() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->remove(2);

        if (!($result instanceof TestObject)) {
            $this->fail();
        }

        $this->assertEquals(4, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Two", $list->get(1)->getValue());
        $this->assertEquals("Four", $list->get(2)->getValue());
        $this->assertEquals("Five", $list->get(3)->getValue());
        $this->assertEquals("Three", $result->getValue());
    }

    public function testIndexOf() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->indexOf((new TestObject())->setValue("Four"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(3, $result);
    }

    public function testIndexOfMultiIndex() {
        $data = new TestCollection(
            TClass::from(TestObject::class), [
            (new TestObject())->setValue("One"),
            (new TestObject())->setValue("Two"),
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Five"),
        ]);

        $list = new ArrayList(TClass::from(TestObject::class), $data);

        $result = $list->indexOf((new TestObject())->setValue("Four"));

        $this->assertEquals(6, $list->size());
        $this->assertEquals(3, $result);
    }

    public function testIndexOfNotFound() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->indexOf((new TestObject())->setValue("DoesNotExist"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(-1, $result);
    }

    public function testIndexOfOtherType() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->indexOf((new NotTestObject())->setValue("One"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(-1, $result);
    }

    public function testLastIndexOf() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->lastIndexOf((new TestObject())->setValue("Four"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(3, $result);
    }

    public function testLastIndexOfMultiIndex() {
        $data = new TestCollection(
            TClass::from(TestObject::class), [
            (new TestObject())->setValue("One"),
            (new TestObject())->setValue("Two"),
            (new TestObject())->setValue("Three"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Four"),
            (new TestObject())->setValue("Five"),
        ]);

        $list = new ArrayList(TClass::from(TestObject::class), $data);

        $result = $list->lastIndexOf((new TestObject())->setValue("Four"));

        $this->assertEquals(6, $list->size());
        $this->assertEquals(4, $result);
    }

    public function testLastIndexOfNotFound() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->lastIndexOf((new TestObject())->setValue("DoesNotExist"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(-1, $result);
    }

    public function testLastIndexOfOtherType() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->lastIndexOf((new NotTestObject())->setValue("One"));

        $this->assertEquals(5, $list->size());
        $this->assertEquals(-1, $result);
    }

    public function testNativeFeaturesAddAndGet() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $list[5] = (new TestObject())->setValue("Six");

        $this->assertEquals(6, $list->size());
        $this->assertEquals("Six", $list[5]->getValue());
    }

    public function testNativeFeaturesAddAndGetInvalidType() {
        $this->expectException(IllegalArgumentException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $list[5] = (new NotTestObject())->setValue("Six");
    }

    public function testNativeFeaturesAddAndGetNegativeIndex() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $list[-1] = (new TestObject())->setValue("Six");
    }

    public function testNativeFeaturesAddAndGetIndexTooLarge() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $list[6] = (new TestObject())->setValue("Six");
    }

    public function testNativeFeaturesAddAndGetIndexInTheMiddle() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $list[3] = (new TestObject())->setValue("Six");

        $this->assertEquals(6, $list->size());
        $this->assertEquals("Three", $list[2]->getValue());
        $this->assertEquals("Six", $list[3]->getValue());
        $this->assertEquals("Four", $list[4]->getValue());
    }

    public function testNativeFeaturesOffsetExists() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $this->assertTrue(isset($list[3]));
    }

    public function testNativeFeaturesOffsetDoesNotExists() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $this->assertFalse(isset($list[5]));
    }

    public function testNativeFeaturesOffsetNegativeExists() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        $this->assertFalse(isset($list[-1]));
    }

    public function testNativeFeaturesUnset() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        unset($list[2]);

        $this->assertEquals(4, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Two", $list->get(1)->getValue());
        $this->assertEquals("Four", $list->get(2)->getValue());
        $this->assertEquals("Five", $list->get(3)->getValue());
    }

    public function testNativeFeaturesUnsetIndexTooLarge() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        unset($list[5]);
    }

    public function testNativeFeaturesUnsetIndexNegative() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        unset($list[-1]);
    }

    public function testNativeFeaturesUnsetIndex() {
        $this->expectException(IndexOutOfBoundsException::class);
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        unset($list[-1]);
    }

    public function testNativeFeaturesUnsetEndOfIndex() {
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);
        unset($list[4]);

        $this->assertEquals(4, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Two", $list->get(1)->getValue());
        $this->assertEquals("Three", $list->get(2)->getValue());
        $this->assertEquals("Four", $list->get(3)->getValue());
    }

    public function testRemoveIf() {
        $input = Predicate::of(fn(TestObject $value) => $value->getValue() === "Two");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->removeIf($input);

        $this->assertTrue($result);
        $this->assertEquals(4, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Three", $list->get(1)->getValue());
        $this->assertEquals("Four", $list->get(2)->getValue());
        $this->assertEquals("Five", $list->get(3)->getValue());
    }

    public function testRemoveIfNotFound() {
        $input = Predicate::of(fn(TestObject $value) => $value->getValue() === "NotFound");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $result = $list->removeIf($input);

        $this->assertFalse($result);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("One", $list->get(0)->getValue());
        $this->assertEquals("Two", $list->get(1)->getValue());
        $this->assertEquals("Three", $list->get(2)->getValue());
        $this->assertEquals("Four", $list->get(3)->getValue());
        $this->assertEquals("Five", $list->get(4)->getValue());
    }

    public function testRemoveIfInvalidDataType() {
        $this->expectException(TypeError::class);
        $input = Predicate::of(fn(NotTestObject $value) => $value->getValue() === "NotFound");
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->removeIf($input);
    }

    public function testForEach() {
        $input = Consumer::of(fn(TestObject $value) => $value->setValue($value->getValue() . "Tester"));
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->forEach($input);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("OneTester", $list->get(0)->getValue());
        $this->assertEquals("TwoTester", $list->get(1)->getValue());
        $this->assertEquals("ThreeTester", $list->get(2)->getValue());
        $this->assertEquals("FourTester", $list->get(3)->getValue());
        $this->assertEquals("FiveTester", $list->get(4)->getValue());
    }

    public function testForEachInvalidDataType() {
        $this->expectException(TypeError::class);
        $input = Consumer::of(fn(NotTestObject $value) => $value->setValue($value->getValue() . "Tester"));
        $list = new ArrayList(TClass::from(TestObject::class), $this->data);

        $list->forEach($input);

        $this->assertEquals(5, $list->size());
        $this->assertEquals("OneTester", $list->get(0)->getValue());
        $this->assertEquals("TwoTester", $list->get(1)->getValue());
        $this->assertEquals("ThreeTester", $list->get(2)->getValue());
        $this->assertEquals("FourTester", $list->get(3)->getValue());
        $this->assertEquals("FiveTester", $list->get(4)->getValue());
    }
}