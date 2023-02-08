<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Package;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tenant_id' => Tenant::create(),
        ];
    }

    // public function quarterly()
    // {
    //     $p = Package::factory()->create();
    //     $taxAmount = $p->price_quarterly * $p->tax;
    //     $totalAmount = $taxAmount + $p->price_quarterly;

    //     return $this->state(function (array $attributes) use ($p, $taxAmount, $totalAmount) {
    //         return [
    //             'package_id' => $p->id,
    //             'package_price_quarterly' => $p->price_quarterly,
    //             'package_price_yearly' => $p->price_yearly,
    //             'package_tax' => $p->tax,
    //             'period' => 'quarterly',
    //             'tax_amount' => $taxAmount,
    //             'total_amount' => $totalAmount,
    //         ];
    //     });
    // }

    // public function yearly()
    // {
    //     $p = Package::factory()->create();
    //     $taxAmount = $p->price_yearly * $p->tax;
    //     $totalAmount = $taxAmount + $p->price_yearly;

    //     return $this->state(function (array $attributes) use ($p, $taxAmount, $totalAmount) {
    //         return [
    //             'package_id' => $p->id,
    //             'package_price_quarterly' => $p->price_quarterly,
    //             'package_price_yearly' => $p->price_yearly,
    //             'package_tax' => $p->tax,
    //             'period' => 'yearly',
    //             'tax_amount' => $taxAmount,
    //             'total_amount' => $totalAmount,
    //         ];
    //     });
    // }
}
