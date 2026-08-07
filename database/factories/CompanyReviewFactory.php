<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyReview;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<CompanyReview>
 */
final class CompanyReviewFactory extends Factory
{
    /**
     * The nine tags customers pick from as advantages/disadvantages —
     * matches the fixed enum I said we'd ship in the review-module spec.
     * Kept in sync between here and the frontend form via a single
     * upstream config once Phase 2 lands.
     *
     * @var array<int, string>
     */
    private const TAGS = [
        'friendly', 'professional', 'fast', 'on-time', 'reliable',
        'careful', 'fair-pricing', 'communicative', 'value-for-money',
    ];

    /**
     * @var array<int, string>
     */
    private const SOURCES = ['google', 'referred', 'website', 'social', 'other'];

    protected $model = CompanyReview::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $rating = $this->faker->numberBetween(3, 5);
        $advantages = $this->faker->boolean(60)
            ? $this->faker->randomElements(self::TAGS, $this->faker->numberBetween(1, 3))
            : [];
        $disadvantages = $rating < 4 || $this->faker->boolean(25)
            ? $this->faker->randomElements(self::TAGS, 1)
            : [];

        return [
            'company_id' => Company::factory(),
            'user_id' => null,
            'author_name' => $name,
            'author_email' => $this->faker->unique()->safeEmail(),
            'author_initials' => CompanyReview::initialsFor($name),
            'is_anonymous' => $this->faker->boolean(15),
            'rating' => $rating,
            'body' => $this->faker->paragraphs($this->faker->numberBetween(1, 3), asText: true),
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'source' => $this->faker->randomElement(self::SOURCES),
            'proof_document' => null,
            'status' => 'published',
            'reply_body' => null,
            'replied_at' => null,
            'reply_by_user_id' => null,
            'helpful_count' => $this->faker->numberBetween(0, 25),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'email_verified_at' => now()->subDays($this->faker->numberBetween(1, 90)),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 180)),
        ];
    }

    /**
     * State: attach a company reply (e.g. "thank you for your review").
     * `replied_at` sits at least a day AFTER `published_at` so timelines
     * on the public detail page render in the expected order.
     */
    public function withReply(?string $body = null): self
    {
        return $this->state(fn (array $attrs): array => [
            'reply_body' => $body ?? $this->faker->paragraph(),
            'replied_at' => isset($attrs['published_at'])
                ? Carbon::parse($attrs['published_at'])->addDays($this->faker->numberBetween(1, 5))
                : now()->subDays($this->faker->numberBetween(0, 30)),
        ]);
    }

    /**
     * Convenience states for building realistic distributions in seeders.
     */
    public function rating(int $stars): self
    {
        return $this->state(['rating' => max(1, min(5, $stars))]);
    }

    public function hidden(): self
    {
        return $this->state(['status' => 'hidden']);
    }

    public function anonymous(): self
    {
        return $this->state(['is_anonymous' => true]);
    }
}
