<?php

namespace Tests\Unit;

use App\Models\City;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CityTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testCreateCity()
    {
        City::factory()->create();
        static::assertTrue(true);
    }
}
