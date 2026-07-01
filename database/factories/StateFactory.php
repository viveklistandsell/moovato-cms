<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<State>
 */
final class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition(): array
    {
        $name = $this->faker->state();

        return [
            'country_id' => Country::factory(),
            'name' => $name,
            'code' => mb_strtoupper($this->faker->unique()->lexify('??')),
            'permalink' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
