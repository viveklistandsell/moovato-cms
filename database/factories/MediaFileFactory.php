<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaFile>
 */
final class MediaFileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $original = $this->faker->unique()->word().'.jpg';
        $stored = $this->faker->unique()->sha1().'.jpg';

        return [
            'folder_id' => null,
            'user_id' => null,
            'name' => $original,
            'original_name' => $original,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => $this->faker->numberBetween(1024, 5_000_000),
            'disk' => 'public',
            'path' => 'media/test/'.$stored,
            'thumb_path' => null,
            'medium_path' => null,
            'metadata' => null,
        ];
    }
}
