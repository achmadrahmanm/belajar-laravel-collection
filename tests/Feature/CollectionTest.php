<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Data\Person;

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

    public function testCrud()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Test adding an item
        $collection->push(6);
        $this->assertEquals(6, $collection->last());

        // Test removing an item
        $collection->pop();
        $this->assertEquals(5, $collection->count());

        // Test checking if an item exists
        $this->assertTrue($collection->contains(3));
        $this->assertFalse($collection->contains(6));

        // Test getting an item by key
        $this->assertEquals(2, $collection[1]);
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

    public function testMapIntoCollection()
    {
        $collection = collect(['John', 'Jane', 'Doe']);
        $people = $collection->mapInto(Person::class);

        $this->assertCount(3, $people);
        $this->assertInstanceOf(Person::class, $people[0]);
        $this->assertEquals('John', $people[0]->name);
        $this->assertEquals('Jane', $people[1]->name);
        $this->assertEquals('Doe', $people[2]->name);
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['John', 30],
            ['Jane', 25],
            ['Doe', 40],
        ]);

        $people = $collection->mapSpread(function ($name, $age) {
            return new Person("$name is $age years old");
        });

        $this->assertEquals([
            new Person('John is 30 years old'),
            new Person('Jane is 25 years old'),
            new Person('Doe is 40 years old')
        ], $people->all());
    }

    public function testMapToGroup()
    {
        $collection = collect([
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
            ['name' => 'Doe', 'age' => 40],
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
            ['name' => 'Charlie', 'age' => 40],
            ['name' => 'Eve', 'age' => 30],
            ['name' => 'Frank', 'age' => 25],
            ['name' => 'Grace', 'age' => 40],
            ['name' => 'Hank', 'age' => 30],

        ]);

        $grouped = $collection->mapToGroups(function ($item) {
            return [
                $item['age'] => $item['name']
            ];
        });

        // echo $grouped;
        $this->assertEquals([
            "30" => collect(['John', 'Alice', 'Eve', 'Hank']),
            "25" => collect(['Jane', 'Bob', 'Frank']),
            "40" => collect(['Doe', 'Charlie', 'Grace'])
        ], $grouped->all());
    }

    public function testZip()
    {
        $collection1 = collect(['John', 'Jane', 'Doe']);
        $collection2 = collect([30, 25, 40]);

        $zipped = $collection1->zip($collection2);

        $this->assertEquals([
            collect(['John', 30]),
            collect(['Jane', 25]),
            collect(['Doe', 40])
        ], $zipped->all());
    }

    public function testConcat()
    {
        $collection1 = collect(['John', 'Jane']);
        $collection2 = collect(['Doe', 'Alice']);

        $concatenated = $collection1->concat($collection2);

        $this->assertEquals(['John', 'Jane', 'Doe', 'Alice'], $concatenated->all());
    }

    public function testCombine()
    {
        $keys = collect(['name', 'age']);
        $values = collect(['John', 30]);

        $combined = $keys->combine($values);

        $this->assertEquals([
            'name' => 'John',
            'age' => 30
        ], $combined->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            collect(['John', 'Jane']),
            collect(['Doe', 'Alice']),
            collect(['Bob'])
        ]);

        $collapsed = $collection->collapse();

        $this->assertEquals(['John', 'Jane', 'Doe', 'Alice', 'Bob'], $collapsed->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            ['name' => 'John', 'hobbies' => ['Reading', 'Traveling'], 'age' => 30],
            ['name' => 'Jane', 'hobbies' => ['Cooking'], 'age' => 25],
            ['name' => 'Doe', 'hobbies' => ['Gaming', 'Hiking'], 'age' => 40],
        ]);

        $flatMapped = $collection->flatMap(function ($item) {
            return [$item['hobbies']];
        });

        $this->assertEquals([['Reading', 'Traveling'], ['Cooking'], ['Gaming', 'Hiking']], $flatMapped->all());
    }
}
