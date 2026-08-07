<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds 5 realistic German reviews for every seeded company plus a
 * company reply on 2 of the 5. Idempotent — skips any company that
 * already has reviews so re-running the seeder in dev is safe.
 *
 * Runs AFTER CompanySeeder; if no companies exist yet it exits quietly
 * so `php artisan db:seed` on a fresh DB doesn't blow up on the first
 * pass.
 */
final class CompanyReviewSeeder extends Seeder
{
    /**
     * Hand-picked German review bodies covering the rating spectrum.
     * Feels natural on the seeded Berlin Umzugsunternehmen and gives us
     * a realistic mix to test filtering/sorting in Phase 5.
     *
     * @var array<int, array{rating: int, body: string, advantages: array<int,string>, disadvantages: array<int,string>, source: string}>
     */
    private const REVIEW_POOL = [
        [
            'rating' => 5,
            'body' => 'Wir waren begeistert. Das Team von Moovato war pünktlich, freundlich und hat unseren gesamten Hausrat mit größter Sorgfalt verpackt. Auch das Klavier haben sie ohne einen Kratzer transportiert. Preis-Leistung absolut fair — jederzeit wieder!',
            'advantages' => ['friendly', 'professional', 'careful', 'on-time'],
            'disadvantages' => [],
            'source' => 'google',
        ],
        [
            'rating' => 5,
            'body' => 'Von der ersten Anfrage bis zur letzten Kiste alles top. Der Kostenvoranschlag war transparent, keine versteckten Gebühren. Am Umzugstag stand das Team pünktlich vor der Tür und alles war innerhalb von 4 Stunden erledigt. Klare Empfehlung!',
            'advantages' => ['fast', 'reliable', 'fair-pricing', 'communicative'],
            'disadvantages' => [],
            'source' => 'referred',
        ],
        [
            'rating' => 4,
            'body' => 'Alles hat gut geklappt. Die Möbelmontage war einwandfrei und die Mitarbeiter sehr höflich. Einzig die Kommunikation im Vorfeld hätte etwas besser sein können — es dauerte 2 Tage, bis wir einen Rückruf bekamen. Trotzdem ein guter Umzug!',
            'advantages' => ['professional', 'friendly'],
            'disadvantages' => ['communicative'],
            'source' => 'website',
        ],
        [
            'rating' => 5,
            'body' => 'Super Team, super Service. Wir sind aus einer 4-Zimmer-Wohnung im 3. Stock (ohne Aufzug!) in ein Haus umgezogen. Alles ist heil angekommen. Danke an das gesamte Team!',
            'advantages' => ['friendly', 'fast', 'careful'],
            'disadvantages' => [],
            'source' => 'google',
        ],
        [
            'rating' => 3,
            'body' => 'Der Umzug an sich verlief ordentlich. Leider gab es beim Aufbau ein paar Kratzer am Schrank, die vorher nicht dokumentiert waren. Nach Reklamation hat man sich immerhin schnell gekümmert und eine Erstattung angeboten.',
            'advantages' => ['communicative'],
            'disadvantages' => ['careful'],
            'source' => 'social',
        ],
    ];

    /**
     * Two of the five reviews per company get a company reply. Kept
     * short + polite — matches how good moving companies actually
     * respond on Google/Sirelo.
     *
     * @var array<int, string>
     */
    private const REPLY_POOL = [
        'Vielen Dank für Ihr Vertrauen und die tolle Bewertung! Es hat uns sehr gefreut, Sie beim Umzug unterstützen zu dürfen. Wir wünschen viel Freude im neuen Zuhause. — Ihr Moovato-Team',
        'Wir bedanken uns herzlich für die freundlichen Worte. Feedback wie Ihres motiviert unser Team jeden Tag aufs Neue. Bis zum nächsten Mal!',
        'Danke für die ehrliche Rückmeldung. Wir nehmen die Kritik ernst und arbeiten kontinuierlich daran, unsere Kommunikation zu verbessern. Sollten Sie noch offene Punkte haben, melden Sie sich gerne direkt bei uns.',
    ];

    public function run(): void
    {
        $companies = Company::query()->orderBy('id')->get(['id']);
        if ($companies->isEmpty()) {
            $this->command?->warn('CompanyReviewSeeder skipped — no companies. Run CompanySeeder first.');

            return;
        }

        $adminId = User::query()->orderBy('id')->value('id');

        $totalReviews = 0;
        foreach ($companies as $company) {
            if (CompanyReview::query()->where('company_id', $company->id)->exists()) {
                continue;
            }

            $withReplyIndexes = [0, 2];
            foreach (self::REVIEW_POOL as $i => $review) {
                $publishedAt = Carbon::now()->subDays(($i + 1) * 12);
                $name = fake()->name();

                $row = [
                    'company_id' => $company->id,
                    'user_id' => null,
                    'author_name' => $name,
                    'author_email' => fake()->unique()->safeEmail(),
                    'author_initials' => CompanyReview::initialsFor($name),
                    'is_anonymous' => fake()->boolean(15),
                    'rating' => $review['rating'],
                    'body' => $review['body'],
                    'advantages' => $review['advantages'],
                    'disadvantages' => $review['disadvantages'],
                    'source' => $review['source'],
                    'status' => 'published',
                    'helpful_count' => fake()->numberBetween(0, 40),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'email_verified_at' => $publishedAt->copy()->subHour(),
                    'published_at' => $publishedAt,
                ];

                if (in_array($i, $withReplyIndexes, true) && $adminId !== null) {
                    $row['reply_body'] = self::REPLY_POOL[$i % count(self::REPLY_POOL)];
                    $row['replied_at'] = $publishedAt->copy()->addDays(2);
                    $row['reply_by_user_id'] = $adminId;
                }

                CompanyReview::query()->create($row);
                $totalReviews++;
            }
        }

        foreach ($companies as $company) {
            Company::recomputeReviewStats((int) $company->id);
        }

        $this->command?->info("CompanyReviewSeeder: {$totalReviews} reviews created across {$companies->count()} companies.");
    }
}
