<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\TenantContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TenantContact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_name_by_tenant' => $this->faker->name(),
            'contact_id' => Contact::factory(),
            'tenant_id' => 4,
        ];
    }
}
