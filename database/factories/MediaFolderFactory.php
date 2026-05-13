<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaFolder>
 */
final class MediaFolderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'parent_id' => null,
            'user_id' => null,
            'name' => $name,
            'path' => $name,
        ];
    }
}
