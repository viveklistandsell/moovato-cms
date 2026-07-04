<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceCategory>
 */
final class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name' => $this->faker->unique()->words(2, true),
            'icon' => null,
            'is_featured' => false,
            'is_popular' => false,
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
