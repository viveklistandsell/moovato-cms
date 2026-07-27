<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyFaq;
use App\Models\District;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Seeds 3 realistic Berlin Umzugsunternehmen (German-first + English)
 * plus their translations, contacts, service pivots, service-area
 * pivots (all 12 Berlin Bezirke each), and 3 FAQs each.
 *
 * Idempotent — keyed on the German permalink of the company's default
 * translation. Re-running only updates existing rows.
 *
 * Prerequisites: CitySeeder (Berlin), DistrictSeeder (Berlin's 12
 * Bezirke), ServiceCategorySeeder (at least Privatumzug + Firmenumzug +
 * Fernumzug + Umzugshelfer) must have run first.
 */
final class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $berlin = City::query()->where('permalink', 'berlin')->first();
        if ($berlin === null) {
            $this->command?->warn('CompanySeeder skipped — Berlin not found. Run CitySeeder first.');

            return;
        }

        $districts = District::query()
            ->where('city_id', $berlin->id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'permalink'])
            ->keyBy('permalink');

        if ($districts->isEmpty()) {
            $this->command?->warn('CompanySeeder skipped — no districts. Run DistrictSeeder first.');

            return;
        }

        $services = collect(['Privatumzug', 'Firmenumzug', 'Fernumzug', 'Umzugshelfer', 'Büroumzug', 'Verpackungsservice'])
            ->mapWithKeys(function (string $name): array {
                $id = ServiceCategory::query()
                    ->whereHas('translations', fn ($q) => $q->where('lang', 'de')->where('name', $name))
                    ->value('id');

                return [$name => $id];
            })
            ->filter();

        foreach ($this->catalogue($berlin->id, $districts, $services) as $entry) {
            $company = $this->upsertCompany($entry);
            $this->syncTranslations($company, $entry['translations']);
            $this->syncContacts($company, $entry['contacts']);
            $this->syncServices($company, $entry['services']);
            $this->syncAreas($company, $entry['areas']);
            $this->syncFaqs($company, $entry['faqs']);
        }

        $this->command?->info('Seeded '.count($this->catalogue($berlin->id, $districts, $services)).' companies for Berlin.');
    }

    /**
     * @param  Collection<string, District>  $districts
     * @param  Collection<string, int>  $services
     * @return list<array<string, mixed>>
     */
    private function catalogue(int $berlinId, $districts, $services): array
    {
        $bezirkIds = $districts->pluck('id')->all();
        $mitteId = $districts->get('mitte')?->id;
        $kreuzbergId = $districts->get('friedrichshain-kreuzberg')?->id;
        $neukoellnId = $districts->get('neukoelln')?->id;

        return [
            [
                'primary_city_id' => $berlinId,
                'primary_district_id' => $mitteId,
                'street' => 'Torstraße 108',
                'postal_code' => '10119',
                'logo' => null,
                'cover' => null,
                'verified' => true,
                'is_top_rated' => true,
                'plan_tier' => 'gold',
                'rating_avg' => 9.7,
                'review_count' => 284,
                'recommend_pct' => 98,
                'google_rating' => 4.9,
                'google_review_count' => 156,
                'founded_year' => 2011,
                'employee_count' => 32,
                'status' => 'published',
                'sort_order' => 1,
                'translations' => [
                    ['lang' => 'de', 'name' => 'Moovato Umzüge Berlin', 'permalink' => 'moovato-umzuege-berlin',
                        'short_description' => 'Ihr zuverlässiges Umzugsunternehmen in Berlin — von Mitte bis Spandau.',
                        'about' => 'Moovato ist ein modernes Umzugsunternehmen mit Sitz in Berlin-Mitte. Seit 2011 organisieren wir Privatumzüge, Firmenumzüge und Fernumzüge in ganz Deutschland und Europa. Unser 32-köpfiges Team bietet Ihnen einen Rundum-Service inklusive Verpackung, Montage und Entsorgung. Alle Fahrzeuge sind versichert, alle Mitarbeiter geschult und sozialversichert. Vertrauen Sie auf über 280 positive Bewertungen und einen Weiterempfehlungswert von 98 %.'],
                    ['lang' => 'en', 'name' => 'Moovato Movers Berlin', 'permalink' => 'moovato-movers-berlin',
                        'short_description' => 'Your reliable moving company in Berlin — from Mitte to Spandau.',
                        'about' => 'Moovato is a modern moving company headquartered in Berlin-Mitte. Since 2011 we have handled private moves, corporate relocations and long-distance moves across Germany and Europe. Our team of 32 delivers a full-service experience including packing, assembly and disposal. All vehicles are insured, all staff trained and fully employed. Trust more than 280 positive reviews and a 98 % recommendation rate.'],
                ],
                'contacts' => [
                    ['type' => 'phone', 'value' => '030 12345678', 'label' => 'Zentrale', 'is_primary' => true],
                    ['type' => 'email', 'value' => 'hallo@moovato.de', 'label' => null, 'is_primary' => true],
                    ['type' => 'website', 'value' => 'https://moovato.de', 'label' => null, 'is_primary' => false],
                    ['type' => 'whatsapp', 'value' => '+491701234567', 'label' => null, 'is_primary' => false],
                ],
                'services' => array_values(array_filter([
                    ['id' => $services['Privatumzug'] ?? null, 'is_primary' => true, 'price_from' => 299, 'price_unit' => 'pro Umzug'],
                    ['id' => $services['Firmenumzug'] ?? null, 'is_primary' => false, 'price_from' => 799, 'price_unit' => 'pro Auftrag'],
                    ['id' => $services['Fernumzug'] ?? null, 'is_primary' => false, 'price_from' => 1290, 'price_unit' => 'pro Umzug'],
                    ['id' => $services['Verpackungsservice'] ?? null, 'is_primary' => false, 'price_from' => 89, 'price_unit' => 'pro Stunde'],
                ], fn ($s) => $s['id'] !== null)),
                'areas' => array_map(
                    fn ($id) => ['district_id' => $id, 'is_home_base' => $id === $mitteId, 'response_hours' => 24],
                    $bezirkIds,
                ),
                'faqs' => [
                    [
                        'de' => ['q' => 'Was macht Moovato besonders?', 'a' => 'Wir sind ein modernes Umzugsunternehmen mit Fokus auf Transparenz. Alle Preise vorab fixiert, keine versteckten Kosten, alle Mitarbeiter fest angestellt und versichert.'],
                        'en' => ['q' => 'What makes Moovato special?', 'a' => 'We are a modern moving company focused on transparency. All prices fixed upfront, no hidden costs, all staff fully employed and insured.'],
                    ],
                    [
                        'de' => ['q' => 'Wie sieht Ihre Versicherung aus?', 'a' => 'Jeder Umzug ist über unsere Speditionshaftpflicht abgesichert. Auf Wunsch buchen wir zusätzlich eine Neuwertversicherung — sprechen Sie uns bei der Anfrage darauf an.'],
                        'en' => ['q' => 'What is your insurance policy?', 'a' => 'Every move is covered by our carrier liability insurance. On request we also add a full replacement-value insurance — just mention it in your quote request.'],
                    ],
                    [
                        'de' => ['q' => 'Welchen Rat geben Sie Ihren Kunden?', 'a' => 'Vereinbaren Sie einen kostenlosen Vor-Ort-Termin. So können wir die Umzugskubik genau bestimmen und Sie bekommen einen verbindlichen Festpreis — keine Überraschungen am Umzugstag.'],
                        'en' => ['q' => 'What is the best advice for your customers?', 'a' => 'Book a free on-site survey. It lets us measure the exact volume so you get a binding fixed price — no surprises on moving day.'],
                    ],
                ],
            ],
            [
                'primary_city_id' => $berlinId,
                'primary_district_id' => $kreuzbergId,
                'street' => 'Oranienstraße 40',
                'postal_code' => '10999',
                'logo' => null,
                'cover' => null,
                'verified' => true,
                'is_top_rated' => false,
                'plan_tier' => 'silver',
                'rating_avg' => 9.3,
                'review_count' => 147,
                'recommend_pct' => 94,
                'google_rating' => 4.7,
                'google_review_count' => 89,
                'founded_year' => 2015,
                'employee_count' => 14,
                'status' => 'published',
                'sort_order' => 2,
                'translations' => [
                    ['lang' => 'de', 'name' => 'Zügig Berlin Umzüge GmbH', 'permalink' => 'zuegig-berlin-umzuege',
                        'short_description' => 'Schnell, ehrlich, bezahlbar — Ihr Umzugsteam aus Kreuzberg.',
                        'about' => 'Die Zügig Berlin Umzüge GmbH ist ein kleines familiengeführtes Umzugsunternehmen aus Kreuzberg. Seit 2015 helfen wir Berlinern bei Privatumzügen, Büroumzügen und Entrümpelungen. Faire Preise, transparente Kalkulation und ein Team, das anpackt statt zu erklären.'],
                    ['lang' => 'en', 'name' => 'Zügig Berlin Movers GmbH', 'permalink' => 'zuegig-berlin-movers',
                        'short_description' => 'Fast, honest, affordable — your moving crew from Kreuzberg.',
                        'about' => 'Zügig Berlin Movers is a small family-run moving company from Kreuzberg. Since 2015 we have helped Berliners with private moves, office relocations and clearance work. Fair prices, transparent quotes, and a team that gets the job done.'],
                ],
                'contacts' => [
                    ['type' => 'phone', 'value' => '030 98765432', 'label' => null, 'is_primary' => true],
                    ['type' => 'email', 'value' => 'info@zuegig-berlin.de', 'label' => null, 'is_primary' => true],
                    ['type' => 'website', 'value' => 'https://zuegig-berlin.de', 'label' => null, 'is_primary' => false],
                ],
                'services' => array_values(array_filter([
                    ['id' => $services['Privatumzug'] ?? null, 'is_primary' => true, 'price_from' => 249, 'price_unit' => 'pro Umzug'],
                    ['id' => $services['Umzugshelfer'] ?? null, 'is_primary' => false, 'price_from' => 35, 'price_unit' => 'pro Stunde'],
                    ['id' => $services['Büroumzug'] ?? null, 'is_primary' => false, 'price_from' => 599, 'price_unit' => 'pro Auftrag'],
                ], fn ($s) => $s['id'] !== null)),
                'areas' => array_map(
                    fn ($id) => ['district_id' => $id, 'is_home_base' => $id === $kreuzbergId, 'response_hours' => 12],
                    $bezirkIds,
                ),
                'faqs' => [
                    [
                        'de' => ['q' => 'Wie schnell antworten Sie auf eine Anfrage?', 'a' => 'Meistens innerhalb von 2–4 Stunden. Bei kurzfristigen Umzügen erreichen Sie uns am besten telefonisch.'],
                        'en' => ['q' => 'How fast do you respond to a request?', 'a' => 'Usually within 2–4 hours. For last-minute moves it is best to call us directly.'],
                    ],
                    [
                        'de' => ['q' => 'Bieten Sie auch Entrümpelung an?', 'a' => 'Ja — Entrümpelung ist Teil unseres Angebots. Wir übernehmen die komplette Räumung inklusive fachgerechter Entsorgung.'],
                        'en' => ['q' => 'Do you also do clearance work?', 'a' => 'Yes — full clearance is part of our service, including proper disposal.'],
                    ],
                    [
                        'de' => ['q' => 'Sind Sie am Wochenende erreichbar?', 'a' => 'Samstags von 8–16 Uhr. Sonntags nach Absprache — ein Wochenendumzug ist bei uns 10 % teurer als werktags.'],
                        'en' => ['q' => 'Are you available on weekends?', 'a' => 'Saturdays 8am–4pm. Sundays by arrangement — weekend moves are 10 % more expensive than weekdays.'],
                    ],
                ],
            ],
            [
                'primary_city_id' => $berlinId,
                'primary_district_id' => $neukoellnId,
                'street' => 'Karl-Marx-Straße 158',
                'postal_code' => '12043',
                'logo' => null,
                'cover' => null,
                'verified' => false,
                'is_top_rated' => false,
                'plan_tier' => 'free',
                'rating_avg' => 8.4,
                'review_count' => 42,
                'recommend_pct' => 87,
                'google_rating' => 4.3,
                'google_review_count' => 28,
                'founded_year' => 2020,
                'employee_count' => 6,
                'status' => 'published',
                'sort_order' => 3,
                'translations' => [
                    ['lang' => 'de', 'name' => 'Hauptstadt Movers Neukölln', 'permalink' => 'hauptstadt-movers-neukoelln',
                        'short_description' => 'Junges Umzugsteam mit fairen Preisen — spezialisiert auf Studentenumzüge.',
                        'about' => 'Hauptstadt Movers ist ein junges Umzugsunternehmen aus Neukölln. Wir haben uns auf kleinere Umzüge spezialisiert: WG-Zimmer, Studenten, Einzelpersonen. Preiswert, unkompliziert, verlässlich.'],
                    ['lang' => 'en', 'name' => 'Hauptstadt Movers Neukölln', 'permalink' => 'hauptstadt-movers-neukoelln-en',
                        'short_description' => 'Young moving team with fair prices — specialised in student moves.',
                        'about' => 'Hauptstadt Movers is a young moving company from Neukölln. We specialise in smaller moves: shared flats, students, single-person households. Affordable, uncomplicated, reliable.'],
                ],
                'contacts' => [
                    ['type' => 'phone', 'value' => '030 55544433', 'label' => null, 'is_primary' => true],
                    ['type' => 'email', 'value' => 'hi@hauptstadt-movers.de', 'label' => null, 'is_primary' => true],
                    ['type' => 'whatsapp', 'value' => '+491751234567', 'label' => null, 'is_primary' => false],
                ],
                'services' => array_values(array_filter([
                    ['id' => $services['Privatumzug'] ?? null, 'is_primary' => true, 'price_from' => 149, 'price_unit' => 'pro Umzug'],
                    ['id' => $services['Umzugshelfer'] ?? null, 'is_primary' => false, 'price_from' => 25, 'price_unit' => 'pro Stunde'],
                ], fn ($s) => $s['id'] !== null)),
                'areas' => array_map(
                    fn ($id) => ['district_id' => $id, 'is_home_base' => $id === $neukoellnId, 'response_hours' => 48],
                    $bezirkIds,
                ),
                'faqs' => [
                    [
                        'de' => ['q' => 'Machen Sie auch nur einzelne Möbelstücke?', 'a' => 'Ja, auch Einzeltransporte innerhalb Berlins ab 49 €. Klavier ausgenommen.'],
                        'en' => ['q' => 'Do you also transport individual pieces?', 'a' => 'Yes, single-item transports within Berlin start at 49 €. Pianos excluded.'],
                    ],
                    [
                        'de' => ['q' => 'Kann ich beim Umzug mithelfen?', 'a' => 'Klar — viele unserer Studentenkunden packen selbst mit an. Das drückt den Preis nochmal um 15 %.'],
                        'en' => ['q' => 'Can I help during the move?', 'a' => 'Of course — many of our student customers help pack. That drops the price another 15 %.'],
                    ],
                    [
                        'de' => ['q' => 'Wie ist Ihre Absage-Regelung?', 'a' => 'Absage bis 48 Stunden vor Umzug kostenfrei. Danach berechnen wir eine Ausfallpauschale von 30 %.'],
                        'en' => ['q' => 'What is your cancellation policy?', 'a' => 'Free cancellation up to 48 hours before the move. After that we charge a 30 % cancellation fee.'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function upsertCompany(array $entry): Company
    {
        $de = collect($entry['translations'])->firstWhere('lang', 'de');
        $existing = null;
        if ($de !== null) {
            $existing = Company::query()
                ->whereHas('translations', fn ($q) => $q
                    ->where('lang', 'de')
                    ->where('permalink', $de['permalink']))
                ->first();
        }

        $attrs = collect($entry)->only([
            'primary_city_id', 'primary_district_id', 'street', 'postal_code',
            'logo', 'cover', 'verified', 'is_top_rated', 'plan_tier',
            'rating_avg', 'review_count', 'recommend_pct',
            'google_rating', 'google_review_count',
            'founded_year', 'employee_count', 'status', 'sort_order',
        ])->all();

        if ($existing !== null) {
            $existing->update($attrs);

            return $existing;
        }

        return Company::query()->create($attrs);
    }

    /**
     * @param  array<int, array<string, string>>  $translations
     */
    private function syncTranslations(Company $company, array $translations): void
    {
        foreach ($translations as $t) {
            $company->translations()->updateOrCreate(
                ['lang' => $t['lang']],
                [
                    'name' => $t['name'],
                    'permalink' => $t['permalink'],
                    'short_description' => $t['short_description'] ?? null,
                    'about' => $t['about'] ?? null,
                ],
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    private function syncContacts(Company $company, array $contacts): void
    {
        $company->contacts()->delete();
        foreach ($contacts as $idx => $c) {
            $company->contacts()->create([
                'type' => $c['type'],
                'value' => $c['value'],
                'label' => $c['label'] ?? null,
                'is_primary' => (bool) ($c['is_primary'] ?? false),
                'sort_order' => $idx + 1,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     */
    private function syncServices(Company $company, array $services): void
    {
        $sync = [];
        foreach ($services as $s) {
            if ($s['id'] === null) {
                continue;
            }
            $sync[(int) $s['id']] = [
                'is_primary' => (bool) $s['is_primary'],
                'price_from' => $s['price_from'],
                'price_unit' => $s['price_unit'],
            ];
        }
        $company->services()->sync($sync);
    }

    /**
     * @param  array<int, array<string, mixed>>  $areas
     */
    private function syncAreas(Company $company, array $areas): void
    {
        $sync = [];
        foreach ($areas as $a) {
            $sync[(int) $a['district_id']] = [
                'is_home_base' => (bool) ($a['is_home_base'] ?? false),
                'response_hours' => $a['response_hours'] ?? null,
            ];
        }
        $company->serviceAreas()->sync($sync);
    }

    /**
     * @param  array<int, array<string, array<string, string>>>  $faqs
     */
    private function syncFaqs(Company $company, array $faqs): void
    {
        $company->faqs()->each(fn (CompanyFaq $f) => $f->delete());
        foreach ($faqs as $idx => $faq) {
            $row = $company->faqs()->create([
                'sort_order' => $idx + 1,
                'status' => 'published',
            ]);
            foreach ($faq as $lang => $qa) {
                $row->translations()->create([
                    'lang' => $lang,
                    'question' => $qa['q'],
                    'answer' => $qa['a'],
                ]);
            }
        }
    }
}
