<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use App\Models\BlogTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class BlogPostSeeder extends Seeder
{
    /**
     * @var array<int, array{
     *   en: array{name: string, short: string, content: string},
     *   de: array{name: string, short: string, content: string},
     *   reading_time: int
     * }>
     */
    private array $posts;

    public function run(): void
    {
        $this->posts = $this->postData();

        DB::transaction(function (): void {
            $user = $this->ensureUser();
            $categories = $this->ensureCategories();
            $tags = $this->ensureTags();

            // Wipe existing seeded posts to keep run idempotent.
            Blog::query()->delete();

            foreach ($this->posts as $index => $post) {
                $sortOrder = $index + 1;
                $statuses = ['published', 'published', 'published', 'published', 'draft', 'inactive'];
                $status = $statuses[$index % count($statuses)];

                $created = Blog::query()->create([
                    'name' => $post['de']['name'],
                    'permalink' => $this->slugify($post['de']['name']),
                    'short_description' => $post['de']['short'],
                    'content' => $post['de']['content'],
                    'image' => null,
                    'user_id' => $user->id,
                    'status' => $status,
                    'sort_order' => $sortOrder,
                    'reading_time' => $post['reading_time'],
                    'view_count' => random_int(50, 8000),
                    'is_sticky' => $index < 2,
                    'is_featured' => $index % 3 === 0,
                ]);

                // Attach 1-3 random categories (first one is_primary).
                $catCount = random_int(1, min(3, $categories->count()));
                $catPicks = $categories->random($catCount)->values();
                $sync = [];
                foreach ($catPicks as $i => $c) {
                    $sync[$c->id] = ['is_primary' => $i === 0];
                }
                $created->categories()->sync($sync);

                // Attach 2-4 random tags.
                $tagCount = random_int(2, min(4, $tags->count()));
                $created->tags()->sync($tags->random($tagCount)->pluck('id')->all());

                // German translation row (matches main columns).
                BlogTranslation::query()->create([
                    'blog_id' => $created->id,
                    'lang' => 'de',
                    'name' => $post['de']['name'],
                    'permalink' => $this->slugify($post['de']['name']),
                    'short_description' => $post['de']['short'],
                    'content' => $post['de']['content'],
                ]);

                // English translation row.
                BlogTranslation::query()->create([
                    'blog_id' => $created->id,
                    'lang' => 'en',
                    'name' => $post['en']['name'],
                    'permalink' => $this->slugify($post['en']['name']),
                    'short_description' => $post['en']['short'],
                    'content' => $post['en']['content'],
                ]);
            }
        });
    }

    private function ensureUser(): User
    {
        $user = User::query()->first();
        if ($user !== null) {
            return $user;
        }

        return User::query()->create([
            'name' => 'Demo Author',
            'email' => 'demo@moovato.test',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    private function ensureCategories(): Collection
    {
        if (BlogCategory::query()->count() >= 4) {
            return BlogCategory::query()->limit(8)->get();
        }

        $seed = [
            ['de' => 'Reise', 'en' => 'Travel', 'icon' => 'ti ti-plane'],
            ['de' => 'Technologie', 'en' => 'Technology', 'icon' => 'ti ti-device-laptop'],
            ['de' => 'Essen & Trinken', 'en' => 'Food & Drink', 'icon' => 'ti ti-coffee'],
            ['de' => 'Lifestyle', 'en' => 'Lifestyle', 'icon' => 'ti ti-heart'],
            ['de' => 'Geschäft', 'en' => 'Business', 'icon' => 'ti ti-briefcase'],
            ['de' => 'Bildung', 'en' => 'Education', 'icon' => 'ti ti-book'],
        ];

        $sortOrder = (int) BlogCategory::query()->max('sort_order') + 1;
        foreach ($seed as $row) {
            $cat = BlogCategory::query()->create([
                'name' => $row['de'],
                'permalink' => $this->slugify($row['de']),
                'parent_id' => null,
                'icon' => $row['icon'],
                'short_description' => null,
                'is_featured' => false,
                'is_default' => false,
                'status' => 'published',
                'sort_order' => $sortOrder++,
            ]);
            BlogCategoryTranslation::query()->create([
                'category_id' => $cat->id,
                'lang' => 'de',
                'name' => $row['de'],
                'permalink' => $this->slugify($row['de']),
            ]);
            BlogCategoryTranslation::query()->create([
                'category_id' => $cat->id,
                'lang' => 'en',
                'name' => $row['en'],
                'permalink' => $this->slugify($row['en']),
            ]);
        }

        return BlogCategory::query()->get();
    }

    private function ensureTags(): Collection
    {
        if (BlogTag::query()->count() >= 6) {
            return BlogTag::query()->limit(15)->get();
        }

        $seed = [
            ['de' => 'Anleitung', 'en' => 'Tutorial'],
            ['de' => 'Tipps', 'en' => 'Tips'],
            ['de' => 'Rezension', 'en' => 'Review'],
            ['de' => 'Anfänger', 'en' => 'Beginner'],
            ['de' => 'Fortgeschritten', 'en' => 'Advanced'],
            ['de' => 'Ratgeber', 'en' => 'Guide'],
            ['de' => 'Inspiration', 'en' => 'Inspiration'],
            ['de' => 'Gesundheit', 'en' => 'Health'],
            ['de' => 'Produktivität', 'en' => 'Productivity'],
            ['de' => 'Trends', 'en' => 'Trends'],
        ];

        $sortOrder = (int) BlogTag::query()->max('sort_order') + 1;
        foreach ($seed as $row) {
            $tag = BlogTag::query()->create([
                'name' => $row['de'],
                'permalink' => $this->slugify($row['de']),
                'short_description' => null,
                'status' => 'published',
                'sort_order' => $sortOrder++,
            ]);
            BlogTagTranslation::query()->create([
                'tag_id' => $tag->id,
                'lang' => 'de',
                'name' => $row['de'],
                'permalink' => $this->slugify($row['de']),
            ]);
            BlogTagTranslation::query()->create([
                'tag_id' => $tag->id,
                'lang' => 'en',
                'name' => $row['en'],
                'permalink' => $this->slugify($row['en']),
            ]);
        }

        return BlogTag::query()->get();
    }

    private function slugify(string $value): string
    {
        $slug = mb_strtolower($value);
        $slug = strtr($slug, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = mb_trim($slug, '-');

        // Ensure uniqueness across blogs by appending a counter if needed.
        $base = $slug;
        $i = 1;
        while (
            Blog::query()->where('permalink', $slug)->exists()
            || BlogTranslation::query()->where('permalink', $slug)->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * @return array<int, array{en: array{name: string, short: string, content: string}, de: array{name: string, short: string, content: string}, reading_time: int}>
     */
    private function postData(): array
    {
        $body = fn (string $intro, array $sections) => '<p>'.$intro.'</p>'.
            implode('', array_map(
                fn (array $s) => "<h2>{$s['h']}</h2><p>{$s['p']}</p>",
                $sections,
            ));

        return [
            [
                'reading_time' => 6,
                'de' => [
                    'name' => '10 Tipps für besseren Schlaf',
                    'short' => 'Praktische Strategien, die Ihre Schlafqualität ab heute Nacht verbessern.',
                    'content' => $body('Guter Schlaf ist die Grundlage für Gesundheit und Produktivität. Hier sind zehn forschungsbasierte Tipps.', [
                        ['h' => 'Eine feste Routine', 'p' => 'Gehen Sie jeden Tag zur gleichen Zeit ins Bett — auch am Wochenende.'],
                        ['h' => 'Bildschirme abschalten', 'p' => 'Blaues Licht stört das Melatonin. Eine Stunde vor dem Schlafen offline gehen.'],
                    ]),
                ],
                'en' => [
                    'name' => '10 Tips for Better Sleep',
                    'short' => 'Practical strategies you can apply tonight to improve your sleep quality.',
                    'content' => $body('Good sleep is the foundation of health and productivity. Here are ten research-backed tips.', [
                        ['h' => 'Stick to a schedule', 'p' => 'Go to bed at the same time every day — even on weekends.'],
                        ['h' => 'Cut the screens', 'p' => 'Blue light disrupts melatonin. Go offline an hour before bed.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 9,
                'de' => [
                    'name' => 'Wie man 2026 einen Blog startet',
                    'short' => 'Vom Domain-Kauf bis zum ersten Beitrag — Ihr kompletter Leitfaden.',
                    'content' => $body('Einen Blog zu starten ist 2026 einfacher denn je — und gleichzeitig wettbewerbsintensiver.', [
                        ['h' => 'Wählen Sie Ihre Nische', 'p' => 'Schmal anfangen, breit wachsen.'],
                        ['h' => 'Plattform auswählen', 'p' => 'Statische Seiten, Headless CMS oder klassisches WordPress?'],
                    ]),
                ],
                'en' => [
                    'name' => 'How to Start a Blog in 2026',
                    'short' => 'From domain purchase to your first post — the complete walkthrough.',
                    'content' => $body('Starting a blog is easier than ever in 2026 — and more competitive.', [
                        ['h' => 'Pick your niche', 'p' => 'Start narrow, grow wide.'],
                        ['h' => 'Choose a platform', 'p' => 'Static site, headless CMS or classic WordPress?'],
                    ]),
                ],
            ],
            [
                'reading_time' => 8,
                'de' => [
                    'name' => 'Die Zukunft der KI im Alltag',
                    'short' => 'Wie KI bereits Ihre Arbeit, Kommunikation und Entscheidungen prägt.',
                    'content' => $body('Künstliche Intelligenz ist nicht mehr Zukunftsmusik — sie ist Realität.', [
                        ['h' => 'Arbeit', 'p' => 'KI-Assistenten werden zum Standard-Werkzeug.'],
                        ['h' => 'Kreativität', 'p' => 'Generative Modelle verändern Design und Schreiben.'],
                    ]),
                ],
                'en' => [
                    'name' => 'The Future of AI in Daily Life',
                    'short' => 'How AI is already shaping your work, communication and decisions.',
                    'content' => $body('AI is no longer a future technology — it is here.', [
                        ['h' => 'Work', 'p' => 'AI assistants are becoming standard tools.'],
                        ['h' => 'Creativity', 'p' => 'Generative models are reshaping design and writing.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 5,
                'de' => [
                    'name' => 'Die besten Cafés in Berlin',
                    'short' => 'Unsere kuratierte Liste für Kaffeeliebhaber in der Hauptstadt.',
                    'content' => $body('Berlin ist ein Paradies für Kaffeefans. Hier sind unsere Favoriten.', [
                        ['h' => 'Mitte', 'p' => 'Modern, geschäftig, voller Specialty Coffee.'],
                        ['h' => 'Kreuzberg', 'p' => 'Entspannte Atmosphäre, fairer Handel im Fokus.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Best Coffee Shops in Berlin',
                    'short' => 'Our curated list for coffee lovers in the capital.',
                    'content' => $body('Berlin is a paradise for coffee fans. Here are our favourites.', [
                        ['h' => 'Mitte', 'p' => 'Modern, busy, full of specialty coffee.'],
                        ['h' => 'Kreuzberg', 'p' => 'Relaxed atmosphere with a fair-trade focus.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 12,
                'de' => [
                    'name' => 'Vue 3 vs. React 19: Ein ehrlicher Vergleich',
                    'short' => 'Wann jedes Framework brilliert — und wann nicht.',
                    'content' => $body('Beide Frameworks sind hervorragend. Die Wahl hängt vom Kontext ab.', [
                        ['h' => 'Lernkurve', 'p' => 'Vue ist sanfter, React ist verbreiteter.'],
                        ['h' => 'Ökosystem', 'p' => 'React hat mehr Bibliotheken, Vue hat klarere Konventionen.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Vue 3 vs React 19: An Honest Comparison',
                    'short' => 'When each framework shines — and when it does not.',
                    'content' => $body('Both frameworks are excellent. The choice depends on context.', [
                        ['h' => 'Learning curve', 'p' => 'Vue is gentler, React is more widespread.'],
                        ['h' => 'Ecosystem', 'p' => 'React has more libraries, Vue has clearer conventions.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 7,
                'de' => [
                    'name' => 'Mediterrane Ernährung erklärt',
                    'short' => 'Eine der gesündesten Ernährungsformen — leicht erklärt.',
                    'content' => $body('Olivenöl, Gemüse, Fisch und Vollkorn — das sind die Säulen.', [
                        ['h' => 'Was essen?', 'p' => 'Viel Pflanzliches, mäßig Fisch, wenig rotes Fleisch.'],
                        ['h' => 'Warum funktioniert es?', 'p' => 'Anti-entzündliche Effekte und Herzgesundheit.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Mediterranean Diet Explained',
                    'short' => 'One of the healthiest eating patterns — explained simply.',
                    'content' => $body('Olive oil, vegetables, fish and whole grains — these are the pillars.', [
                        ['h' => 'What to eat?', 'p' => 'Mostly plant-based, moderate fish, little red meat.'],
                        ['h' => 'Why does it work?', 'p' => 'Anti-inflammatory effects and heart health.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 6,
                'de' => [
                    'name' => 'Produktiv im Homeoffice arbeiten',
                    'short' => 'Strategien, die wirklich helfen, wenn Küche und Büro zusammenfallen.',
                    'content' => $body('Homeoffice klingt traumhaft, ist aber harte Arbeit.', [
                        ['h' => 'Klare Grenzen', 'p' => 'Festgelegte Arbeitszeiten und ein dedizierter Bereich.'],
                        ['h' => 'Tiefe Arbeit', 'p' => 'Blockieren Sie ungestörte Stunden für anspruchsvolle Aufgaben.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Working from Home Productivity',
                    'short' => 'Strategies that actually help when kitchen and office become one.',
                    'content' => $body('Working from home sounds like a dream, but it is hard work.', [
                        ['h' => 'Clear boundaries', 'p' => 'Fixed hours and a dedicated space.'],
                        ['h' => 'Deep work', 'p' => 'Block uninterrupted hours for demanding tasks.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 10,
                'de' => [
                    'name' => 'Fotografie für Anfänger',
                    'short' => 'Die ersten Schritte mit Ihrer Kamera — ohne Fachjargon.',
                    'content' => $body('Eine Kamera kann einschüchtern, muss aber nicht.', [
                        ['h' => 'Belichtungsdreieck', 'p' => 'Blende, Verschlusszeit und ISO im Zusammenspiel.'],
                        ['h' => 'Komposition', 'p' => 'Drittelregel, Linien und natürliche Rahmen.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Photography for Beginners',
                    'short' => 'Your first steps with a camera — no jargon required.',
                    'content' => $body('A camera can be intimidating but it does not have to be.', [
                        ['h' => 'Exposure triangle', 'p' => 'Aperture, shutter speed and ISO working together.'],
                        ['h' => 'Composition', 'p' => 'Rule of thirds, leading lines, natural frames.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 11,
                'de' => [
                    'name' => 'Solo-Reisen: Ein praktischer Leitfaden',
                    'short' => 'Sicher, günstig und sinnvoll allein die Welt entdecken.',
                    'content' => $body('Allein zu reisen verändert die Perspektive. Hier sind die Grundlagen.', [
                        ['h' => 'Planung', 'p' => 'Erste Stadt vorbuchen, danach flexibel bleiben.'],
                        ['h' => 'Sicherheit', 'p' => 'Vertraute Person regelmäßig informieren.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Solo Travel: A Practical Guide',
                    'short' => 'How to explore the world alone — safely and affordably.',
                    'content' => $body('Travelling alone changes perspective. Here are the basics.', [
                        ['h' => 'Planning', 'p' => 'Pre-book the first city, then stay flexible.'],
                        ['h' => 'Safety', 'p' => 'Keep someone you trust regularly updated.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 8,
                'de' => [
                    'name' => 'Grundlagen der persönlichen Finanzen',
                    'short' => 'Budget, Notgroschen, Investieren — Schritt für Schritt.',
                    'content' => $body('Geld muss nicht kompliziert sein. Beginnen Sie mit den Basics.', [
                        ['h' => 'Notgroschen', 'p' => 'Drei bis sechs Monatsausgaben auf einem Tagesgeldkonto.'],
                        ['h' => 'Investieren', 'p' => 'Breit gestreute Index-ETFs als Einstieg.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Personal Finance Basics',
                    'short' => 'Budget, emergency fund, investing — step by step.',
                    'content' => $body('Money does not have to be complicated. Start with the basics.', [
                        ['h' => 'Emergency fund', 'p' => 'Three to six months of expenses in a savings account.'],
                        ['h' => 'Investing', 'p' => 'Broad index ETFs are a sensible starting point.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 9,
                'de' => [
                    'name' => 'Web Performance optimieren',
                    'short' => 'Konkrete Techniken, die Ihre Seite messbar schneller machen.',
                    'content' => $body('Schnelle Seiten ranken besser und konvertieren besser.', [
                        ['h' => 'Bilder', 'p' => 'Modernes Format (WebP/AVIF), passende Größe, Lazy Loading.'],
                        ['h' => 'JavaScript', 'p' => 'Code-Splitting und Dead-Code Elimination.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Web Performance Optimization',
                    'short' => 'Concrete techniques that make your site measurably faster.',
                    'content' => $body('Fast sites rank better and convert better.', [
                        ['h' => 'Images', 'p' => 'Modern format (WebP/AVIF), correct size, lazy loading.'],
                        ['h' => 'JavaScript', 'p' => 'Code splitting and dead-code elimination.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 5,
                'de' => [
                    'name' => 'Gesunde Morgenroutinen',
                    'short' => 'Kleine Gewohnheiten mit großer Wirkung auf den Tag.',
                    'content' => $body('Wie Sie aufwachen, prägt den Rest des Tages.', [
                        ['h' => 'Hydration', 'p' => 'Ein Glas Wasser direkt nach dem Aufstehen.'],
                        ['h' => 'Bewegung', 'p' => 'Zehn Minuten Stretching oder ein kurzer Spaziergang.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Healthy Morning Routines',
                    'short' => 'Small habits with a big impact on your day.',
                    'content' => $body('How you wake up sets the tone for the rest of the day.', [
                        ['h' => 'Hydration', 'p' => 'A glass of water right after getting up.'],
                        ['h' => 'Movement', 'p' => 'Ten minutes of stretching or a short walk.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 7,
                'de' => [
                    'name' => 'Nachhaltige Mode-Trends',
                    'short' => 'Stilvoll bleiben und gleichzeitig die Umwelt schonen.',
                    'content' => $body('Mode muss nicht auf Kosten der Umwelt gehen.', [
                        ['h' => 'Second-Hand', 'p' => 'Der grünste Kleiderschrank ist der bestehende.'],
                        ['h' => 'Materialien', 'p' => 'Bio-Baumwolle, Leinen und recycelte Fasern.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Sustainable Fashion Trends',
                    'short' => 'Stay stylish while being kinder to the planet.',
                    'content' => $body('Fashion does not have to come at the cost of the environment.', [
                        ['h' => 'Second-hand', 'p' => 'The greenest wardrobe is the one you already own.'],
                        ['h' => 'Materials', 'p' => 'Organic cotton, linen and recycled fibres.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 6,
                'de' => [
                    'name' => 'Schnell Deutsch lernen',
                    'short' => 'Effektive Methoden für motivierte Anfänger.',
                    'content' => $body('Deutsch hat einen Ruf — aber es ist machbar.', [
                        ['h' => 'Spaced Repetition', 'p' => 'Vokabel-Apps mit Wiederholungs-Algorithmus.'],
                        ['h' => 'Sprachpartner', 'p' => 'Tandem oder Italki für echtes Sprechen.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Learning German Fast',
                    'short' => 'Effective methods for motivated beginners.',
                    'content' => $body('German has a reputation — but it is doable.', [
                        ['h' => 'Spaced repetition', 'p' => 'Vocabulary apps with smart-repeat algorithms.'],
                        ['h' => 'Language partners', 'p' => 'Tandem or Italki for real conversation.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 5,
                'de' => [
                    'name' => 'Ideen für das Homeoffice-Setup',
                    'short' => 'Ergonomie, Beleuchtung und Tools für ein produktives Büro zu Hause.',
                    'content' => $body('Ein gutes Setup zahlt sich täglich aus.', [
                        ['h' => 'Stuhl', 'p' => 'Investieren Sie in Ergonomie, der Rücken dankt.'],
                        ['h' => 'Bildschirm', 'p' => 'Augenhöhe, mindestens 24 Zoll.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Home Office Setup Ideas',
                    'short' => 'Ergonomics, lighting and tools for a productive home office.',
                    'content' => $body('A good setup pays back every single day.', [
                        ['h' => 'Chair', 'p' => 'Invest in ergonomics, your back will thank you.'],
                        ['h' => 'Monitor', 'p' => 'Eye level, at least 24 inches.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 10,
                'de' => [
                    'name' => 'Best Practices beim Programmieren',
                    'short' => 'Code, der Ihre Kollegen (und das zukünftige Sie) lieben werden.',
                    'content' => $body('Sauberer Code ist eine Investition in das Team.', [
                        ['h' => 'Klar benennen', 'p' => 'Ein guter Name ersetzt einen Kommentar.'],
                        ['h' => 'Klein halten', 'p' => 'Eine Funktion, eine Aufgabe.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Coding Best Practices',
                    'short' => 'Code your colleagues (and future you) will love.',
                    'content' => $body('Clean code is an investment in your team.', [
                        ['h' => 'Name clearly', 'p' => 'A good name replaces a comment.'],
                        ['h' => 'Keep it small', 'p' => 'One function, one job.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 6,
                'de' => [
                    'name' => 'Inspiration für Gartendesign',
                    'short' => 'Ideen für kleine Balkone bis hin zu großen Gärten.',
                    'content' => $body('Ein Garten ist ein Rückzugsort — egal wie groß.', [
                        ['h' => 'Zonen', 'p' => 'Trennen Sie Sitzbereich, Beete und Rasen.'],
                        ['h' => 'Bepflanzung', 'p' => 'Heimische Pflanzen brauchen weniger Pflege.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Garden Design Inspiration',
                    'short' => 'Ideas from small balconies to spacious gardens.',
                    'content' => $body('A garden is a retreat — no matter the size.', [
                        ['h' => 'Zones', 'p' => 'Separate seating, beds and lawn.'],
                        ['h' => 'Planting', 'p' => 'Native plants need less care.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 4,
                'de' => [
                    'name' => 'Achtsamkeit für vielbeschäftigte Menschen',
                    'short' => 'Praxis, die in fünf Minuten passt — täglich.',
                    'content' => $body('Achtsamkeit braucht keine Stunden.', [
                        ['h' => 'Atem-Anker', 'p' => 'Drei tiefe Atemzüge zwischen Aufgaben.'],
                        ['h' => 'Body-Scan', 'p' => 'Fünf Minuten vor dem Schlafen.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Mindfulness for Busy People',
                    'short' => 'A practice that fits into five minutes — every day.',
                    'content' => $body('Mindfulness does not require hours.', [
                        ['h' => 'Breath anchor', 'p' => 'Three deep breaths between tasks.'],
                        ['h' => 'Body scan', 'p' => 'Five minutes before sleep.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 11,
                'de' => [
                    'name' => 'Lösungen gegen den Klimawandel',
                    'short' => 'Was jeder Einzelne tun kann — und was politisch passieren muss.',
                    'content' => $body('Klimaschutz beginnt im Alltag und endet in der Politik.', [
                        ['h' => 'Persönlich', 'p' => 'Energie sparen, weniger Fleisch, bewusst reisen.'],
                        ['h' => 'Politisch', 'p' => 'Wählen, sich engagieren, Druck machen.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Climate Change Solutions',
                    'short' => 'What individuals can do — and what policy must change.',
                    'content' => $body('Climate action starts in daily life and ends with policy.', [
                        ['h' => 'Personal', 'p' => 'Save energy, eat less meat, travel mindfully.'],
                        ['h' => 'Political', 'p' => 'Vote, engage, apply pressure.'],
                    ]),
                ],
            ],
            [
                'reading_time' => 7,
                'de' => [
                    'name' => 'Buchempfehlungen 2026',
                    'short' => 'Romane, Sachbücher und Klassiker, die wir dieses Jahr lieben.',
                    'content' => $body('2026 hat schon einige Highlights gebracht. Hier sind unsere Lieblinge.', [
                        ['h' => 'Belletristik', 'p' => 'Spannende Stimmen aus aller Welt.'],
                        ['h' => 'Sachbuch', 'p' => 'Klar geschrieben, wissenschaftlich fundiert.'],
                    ]),
                ],
                'en' => [
                    'name' => 'Book Recommendations 2026',
                    'short' => 'Novels, non-fiction and classics we love this year.',
                    'content' => $body('2026 has already brought several highlights. Here are our favourites.', [
                        ['h' => 'Fiction', 'p' => 'Compelling voices from around the world.'],
                        ['h' => 'Non-fiction', 'p' => 'Clearly written, scientifically grounded.'],
                    ]),
                ],
            ],
        ];
    }
}
