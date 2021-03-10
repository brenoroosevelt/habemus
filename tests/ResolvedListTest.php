<?php
declare(strict_types=1);

namespace Habemus\Test;

use Habemus\ResolvedList;
use PHPUnit\Framework\TestCase;

class ResolvedListTest extends TestCase
{
    public function testShouldCreateAnEmptyResolvedList()
    {
        $resolvedList = new ResolvedList();
        $this->assertTrue($resolvedList->isEmpty());
    }

    public function testShouldAddGetAnItemToResolvedList()
    {
        $resolvedList = new ResolvedList();
        $item = new \stdClass();
        $resolvedList->share('id1', $item);

        $this->assertEquals(1, $resolvedList->count());
        $this->assertEquals($item, $resolvedList->get('id1'));
    }

    public function testShouldCheckForElementsOnTheResolvedList()
    {
        $resolvedList = new ResolvedList();
        $item = new \stdClass();
        $resolvedList->share('id1', $item);

        $this->assertTrue($resolvedList->has('id1'));
        $this->assertFalse($resolvedList->has('id2'));
    }

    public function testShouldDeleteAnItemFromResolvedList()
    {
        $resolvedList = new ResolvedList();
        $resolvedList->share('id1', 1);
        $resolvedList->delete('id1');

        $this->assertEquals(0, $resolvedList->count());
    }

    public function testShouldCountElementsOnTheResolvedList()
    {
        $resolvedList = new ResolvedList();
        $this->assertEquals(0, $resolvedList->count());
        $this->assertTrue($resolvedList->isEmpty());

        $resolvedList->share('id1', 1);
        $resolvedList->share('id2', 2);
        $resolvedList->share('id3', 3);

        $this->assertEquals(3, $resolvedList->count());
        $this->assertFalse($resolvedList->isEmpty());
    }

    public function testShouldReplaceElementsByIdOnTheResolvedList()
    {
        $resolvedList = new ResolvedList();
        $resolvedList->share('id1', 10);
        $resolvedList->share('id1', 20);

        $this->assertEquals(20, $resolvedList->get('id1'));
    }

    public function testShouldCheckForElementsOnTheResolvedListAfterDelete()
    {
        $resolvedList = new ResolvedList();
        $resolvedList->share('id1', 1);
        $resolvedList->delete('id1');
        $this->assertFalse($resolvedList->has('id1'));
    }

    public function testShouldIterateAsArrayElementsOnTheResolvedList()
    {
        $resolvedList = new ResolvedList();
        $resolvedList->share('id1', "value1");
        $resolvedList->share('id2', "value2");
        $resolvedList->share('id3', "value3");
        $resolvedList->share('id4', "value4");

        $items = [];
        foreach ($resolvedList as $id => $value) {
            $items[$id] = $value;
        }

        $this->assertEquals([
            "id1" => "value1",
            "id2" => "value2",
            "id3" => "value3",
            "id4" => "value4",
        ], $items);
    }

    public function testShouldIterateElementsOnTheResolvedList()
    {
        $resolvedList = new ResolvedList();
        $resolvedList->share('id1', "value1");
        $resolvedList->share('id2', "value2");
        $resolvedList->share('id3', "value3");

        $items = [];
        foreach ($resolvedList->getIterator() as $id => $value) {
            $items[$id] = $value;
        }

        $this->assertEquals([
            "id1" => "value1",
            "id2" => "value2",
            "id3" => "value3",
        ], $items);
    }
}
