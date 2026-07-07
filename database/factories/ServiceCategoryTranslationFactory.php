<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ServiceCategoryTranslation>
 */
final class ServiceCategoryTranslationFactory extends Factory
{
    protected $model = ServiceCategoryTranslation::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'category_id' => ServiceCategory::factory(),
            'lang' => 'de',
            'name' => $name,
            'permalink' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'short_description' => $this->faker->sentence(),
        ];
    }
}
