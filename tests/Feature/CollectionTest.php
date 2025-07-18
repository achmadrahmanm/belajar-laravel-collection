<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreteCollection()
    {
        $colection = collect([1, 2, 3, 4, 5]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5], $colection->all());
        $this->assertEquals(5, $colection->count());
    }

    public function testCollectionForEach()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $sum = 0;

        foreach ($collection as $item => $value) {
            $this->assertEquals($item + 1, $value);
        }

        $collection->each(function ($item) use (&$sum) {
            $sum += $item;
        });

        $this->assertEquals(15, $sum);
    }

    public function testCollectionMethods()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Test map method
        $mapped = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEquals([2, 4, 6, 8, 10], $mapped->all());

        // Test filter method
        $filtered = $collection->filter(function ($item) {
            return $item > 2;
        });
        $this->assertEquals([3, 4, 5], $filtered->values()->all());

        // Test reduce method
        $sum = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 0);
        $this->assertEquals(15, $sum);
    }
}
