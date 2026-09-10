<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<District>
 */
final class DistrictFactory extends Factory
{
    protected $model = District::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->city();

        return [
            'city_id' => City::factory(),
            'name' => $name,
            'code' => mb_strtoupper($this->faker->unique()->lexify('???')),
            'permalink' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'postal_code_prefix' => null,
            'is_popular' => false,
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
