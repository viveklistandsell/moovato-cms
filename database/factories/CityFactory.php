<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
final class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $name = $this->faker->city();

        return [
            'state_id' => State::factory(),
            'name' => $name,
            'permalink' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'postal_code' => $this->faker->postcode(),
            'is_popular' => false,
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
