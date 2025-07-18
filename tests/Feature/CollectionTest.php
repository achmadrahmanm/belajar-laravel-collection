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

    public function testJoin()
    {
        $collection = collect([
            'John',
            'Jane',
            'Doe'
        ]);

        $joined = $collection->join(', ');
        $this->assertEquals('John, Jane, Doe', $joined);
        $joined = $collection->join(', ', '_');
        $this->assertEquals('John, Jane_Doe', $joined);
    }

    public function testFilter()
    {
        $collection = collect(["eko" => 1, "budi" => 2, "joko" => 3, "doni" => 4, "siti" => 5]);

        $filtered = $collection->filter(function ($value, $key) {
            return $value > 2;
        });

        $this->assertEquals([
            "joko" => 3,
            "doni" => 4,
            "siti" => 5
        ], $filtered->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        $filtered = $collection->filter(function ($value, $key) {
            return $value % 2 !== 0; // Keep only odd numbers
        });

        // $filtered->values()->all() returns the values without keys
        // $filtered->all() returns the original keys

        $this->assertEquals([1, 3, 5], $filtered->values()->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "eko" => 1,
            "budi" => 2,
            "joko" => 3,
            "doni" => 4,
            "siti" => 5
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value % 2 === 0; // Even numbers
        });

        $this->assertEquals(["budi" => 2, "doni" => 4], $result1->all()); // Even numbers
        $this->assertEquals(["eko" => 1, "joko" => 3, "siti" => 5], $result2->all()); // Odd numbers
    }

    public function testTesting()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Test if the collection is empty
        $this->assertFalse($collection->isEmpty());

        // Test if the collection contains a specific item
        $this->assertTrue($collection->contains(3));
        $this->assertFalse($collection->contains(6));

        // Test the count of items in the collection
        $this->assertEquals(5, $collection->count());

        // Test the first and last items in the collection
        $this->assertEquals(1, $collection->first());
        $this->assertEquals(5, $collection->last());

        // Test if the collection has a specific key
        $this->assertTrue($collection->has(0)); // Key 0 exists
        $this->assertFalse($collection->has(10)); // Key 10 does not exist

        // Test if the collection has any of the specified keys
        $this->assertTrue($collection->hasAny([0, 1])); // At least one key exists
        $this->assertFalse($collection->hasAny([10, 11])); // None of the keys exist

        // Test if the collection contains a specific value
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value === 3; // Value 3 exists
        }));
        $this->assertFalse($collection->contains(function ($value, $key) {
            return $value === 10; // Value 10 does not exist
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
            ['name' => 'Doe', 'age' => 30],
            ['name' => 'Alice', 'age' => 25],
        ]);

        $grouped = $collection->groupBy('age');

        $this->assertEquals([
            30 => collect([['name' => 'John', 'age' => 30], ['name' => 'Doe', 'age' => 30]]),
            25 => collect([['name' => 'Jane', 'age' => 25], ['name' => 'Alice', 'age' => 25]])
        ], $grouped->all());


        $result = $collection->groupBy(function ($item) {
            return $item['age'] >= 30 ? 'adults' : 'children';
        });

        $this->assertEquals([
            'adults' => collect([['name' => 'John', 'age' => 30], ['name' => 'Doe', 'age' => 30]]),
            'children' => collect([['name' => 'Jane', 'age' => 25], ['name' => 'Alice', 'age' => 25]])
        ], $result->all());
    }

    public function testSlice()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Slice the collection from index 1 to the end
        $sliced = $collection->slice(1);
        $this->assertEqualsCanonicalizing([2, 3, 4, 5], $sliced->all());

        // Slice the collection from index 1 to index 3
        $sliced = $collection->slice(0, 2);
        $this->assertEquals([1, 2], $sliced->values()->all());

        // Slice the collection with negative offset
        $sliced = $collection->slice(-2);
        $this->assertEquals([4, 5], $sliced->values()->all());
    }

    public function testTakeSkip()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Take the first 3 items
        $taken = $collection->take(3);
        $this->assertEquals([1, 2, 3], $taken->values()->all());

        // Take items until a condition is met
        $takenUntil = $collection->takeUntil(function ($item) {
            return $item > 3; // Stop taking when the item is greater than 3
        });
        $this->assertEquals([1, 2, 3], $takenUntil->values()->all());

        // Take items while a condition is true
        $takenWhile = $collection->takeWhile(function ($item) {
            return $item < 4; // Keep taking while the item is less than 4
        });
        $this->assertEquals([1, 2, 3], $takenWhile->values()->all());

        // Skip the first 2 items
        $skipped = $collection->skip(2);
        $this->assertEquals([3, 4, 5], $skipped->values()->all());

        // Skip items until a condition is met
        $skippedUntil = $collection->skipUntil(function ($item) {
            return $item > 3; // Start skipping until the item is greater than 3
        });
        $this->assertEquals([4, 5], $skippedUntil->values()->all());

        // Skip items while a condition is true
        $skippedWhile = $collection->skipWhile(function ($item) {
            return $item < 4; // Keep skipping while the item is less than 4
        });
        $this->assertEquals([4, 5], $skippedWhile->values()->all());
    }

    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        // Chunk the collection into groups of 3
        $chunked = $collection->chunk(3);

        $this->assertCount(3, $chunked);
        $this->assertEquals([1, 2, 3], $chunked[0]->values()->all());
        $this->assertEquals([4, 5, 6], $chunked[1]->values()->all());
        $this->assertEquals([7, 8, 9], $chunked[2]->values()->all());

        // Chunk the collection
        $chunked = $collection->chunk(3);

        // Apply transformation using map
        $chunkedWithCallback = $chunked->map(function ($chunk) {
            return $chunk->map(function ($item) {
                return "Item: $item";
            });
        });

        $this->assertCount(3, $chunkedWithCallback);
        $this->assertEquals(['Item: 1', 'Item: 2', 'Item: 3'], $chunkedWithCallback[0]->values()->all());
    }

    public function testRetrieve()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Retrieve the first item in the collection
        $this->assertEquals(1, $collection->first());

        // Retrieve the first item or fail if the collection is empty
        $this->assertEquals(1, $collection->firstOrFail());

        // Retrieve the first item that matches a condition
        $this->assertEquals(3, $collection->first(function ($value, $key) {
            return $value > 2;
        }));

        // Retrieve the first item where a specific key matches a value
        $this->assertEquals(3, $collection->firstWhere(null, 3));

        // Retrieve the last item in the collection
        $this->assertEquals(5, $collection->last());

        // Retrieve the last item that matches a condition
        $this->assertEquals(5, $collection->last(function ($value, $key) {
            return $value > 3;
        }));
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        // Retrieve a random item from the collection
        $randomItem = $collection->random();
        $this->assertTrue($collection->contains($randomItem));

        // Retrieve multiple random items from the collection
        $randomItems = $collection->random(2);
        $this->assertCount(2, $randomItems);
        foreach ($randomItems as $item) {
            $this->assertTrue($collection->contains($item));
        }
    }
}
