<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
final class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
            'iso_code' => mb_strtoupper($this->faker->lexify('??')),
            'phone_code' => '+'.$this->faker->numberBetween(1, 999),
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
