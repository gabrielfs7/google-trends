<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Result;

use GSoares\GoogleTrends\Result\InterestByRegionCollection;
use GSoares\GoogleTrends\Result\InterestByRegionResult;
use PHPUnit\Framework\TestCase;

class InterestByRegionCollectionTest extends TestCase
{
    public function testCanGetResults(): void
    {
        $result = new InterestByRegionResult('US - NY', 'NEW YORK', 100, 0, true);

        $collection = new InterestByRegionCollection('url', $result);

        $this->assertSame('url', $collection->getSearchUrl());
        $this->assertSame(1, $collection->getTotalResults());
        $this->assertSame(
            [
                $result
            ],
            $collection->getResults()
        );
    }

    public function testJsonSerializeCollection(): void
    {
        $this->assertSame(
            [
                'searchUrl' => 'url',
                'totalResults' => 1,
                'results' => [
                    [
                        'geoCode' => 'US - NY',
                        'geoName' => 'NEW YORK',
                        'value' => 100,
                        'maxValueIndex' => 0,
                        'hasData' => true,
                    ]
                ],
            ],
            json_decode(
                json_encode(
                    (new InterestByRegionCollection(
                        'url',
                        new InterestByRegionResult('US - NY', 'NEW YORK', 100, 0, true)
                    ))->jsonSerialize()
                ),
                true
            )
        );
    }
}
