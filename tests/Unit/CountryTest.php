<?php

namespace Tests\Unit;

use App\Models\Country;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CountryTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testCreateCountry()
    {
        Country::factory()->create();
        static::assertTrue(true);
    }
}
