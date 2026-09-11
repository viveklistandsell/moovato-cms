<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data-only migration. When the checklist `points`/`features` fields were
 * converted from plain strings to `{title, description}` objects, existing
 * page_widget_translation rows got their old string wrapped as `title` with
 * `description` left empty (see 2026_09_09_120000_backfill_checklist_point_objects).
 * The widgets' defaultData() was since given real German description copy
 * per point, but that only seeds *new* widget instances — it never touches
 * rows already saved on live pages. Backfill the same copy into any existing
 * item whose title matches a known default and whose description is still
 * empty.
 *
 * Idempotent: only items with an empty description are touched, and re-running
 * is a no-op once filled.
 */
return new class extends Migration
{
    /** @var array<string, array<int, string>> */
    private const FIELDS_BY_TYPE = [
        'text_columns' => ['points'],
        'mission_vision' => ['panel_one_points', 'panel_two_points'],
        'dark_feature' => ['points'],
        'dark_intro' => ['points'],
        'content_collage' => ['points'],
        'promo_cta' => ['features'],
        'cta_banner' => ['points'],
        'about_experience' => ['points'],
        'media_checklist' => ['points'],
        'why_choose_media' => ['points'],
        'supporting_media' => ['points'],
        'content_style_1' => ['points'],
        'content_style_2' => ['points'],
        'team_cta' => ['points'],
        'split_media' => ['points'],
        'split_media_left' => ['points'],
    ];

    /** @var array<string, string> */
    private const DESCRIPTIONS = [
        'Maßgeschneiderte Umzugslösungen' => 'Jeder Umzug wird individuell geplant – abgestimmt auf Wohnungsgröße, Zeitplan und persönliche Wünsche.',
        'Verlässliche Partnerschaft' => 'Von der ersten Anfrage bis zum letzten Karton sind wir ein fester Ansprechpartner an Ihrer Seite.',
        'Höchster Anspruch an Qualität' => 'Geschultes Personal und eine sorgfältige Arbeitsweise sichern ein Ergebnis, auf das Sie sich verlassen können.',
        'Innovativer Ansatz' => 'Wir entwickeln unseren Service stetig weiter, um Umzüge in Berlin einfacher und transparenter zu machen.',
        'Kundenorientierung' => 'Ihre Zufriedenheit steht im Mittelpunkt jeder Entscheidung, die wir treffen.',
        'Regionale Verwurzelung' => 'Als Berliner Unternehmen kennen wir die Stadt, ihre Bezirke und die Herausforderungen vor Ort.',
        'Termintreue statt Unsicherheit' => 'Der vereinbarte Termin steht fest – Sie können sich auf den geplanten Ablauf verlassen.',
        'Klare Festpreise statt versteckter Kosten' => 'Ihr Angebot weist jede Position einzeln aus, sodass keine Überraschungen entstehen.',
        'Sorgfältiger Umgang mit Ihrem Inventar' => 'Wir verpacken und transportieren Ihr Hab und Gut, als wäre es unser eigenes.',
        'Komplettservice aus einer Hand' => 'Von der Besichtigung über die Verpackung bis zur Montage übernehmen wir jeden Schritt.',
        'Erfahrenes, freundliches Team' => 'Unsere Umzugsprofis bringen jahrelange Erfahrung und ein freundliches Auftreten mit.',
        'Privatumzug' => 'Ihr Wohnungswechsel in Berlin – von der Einzimmerwohnung bis zum Familienhaus.',
        'Gewerbeumzug' => 'Büro- und Firmenumzüge mit minimaler Ausfallzeit für Ihr Unternehmen.',
        'Fernumzug' => 'Zuverlässiger Transport deutschlandweit, egal wie weit der Weg ist.',
        'Spezialtransport' => 'Sicherer Transport für Klaviere, Kunstwerke und andere empfindliche Gegenstände.',
        'Festpreisgarantie' => 'Der vereinbarte Preis gilt – auch wenn der Umzugstag einmal länger dauert.',
        'Kostenlose Beratung' => 'Wir besprechen Ihren Umzug unverbindlich und finden die passende Lösung.',
        'Versichert & geprüft' => 'Ihr Hab und Gut ist während des gesamten Transports abgesichert.',
        'Erfahrene Umzugsprofis' => 'Unser Team packt, trägt und montiert mit jahrelanger Praxis.',
        'Privat, Gewerbe, Fernumzug & Spezialtransport' => 'Wir decken jede Art von Umzug ab – ganz gleich, wie groß oder klein.',
        'Engagierter, zuverlässiger und stressfreier Service' => 'Unser Team sorgt dafür, dass Ihr Umzugstag entspannt und reibungslos verläuft.',
        'Kundenorientiert mit transparenter Kommunikation' => 'Sie erfahren jederzeit, was als Nächstes ansteht – ohne versteckte Überraschungen.',
        'Für Privatkunden und Unternehmen mit Sorgfalt' => 'Ob Wohnung oder Büro – wir behandeln jeden Auftrag mit derselben Sorgfalt.',
        'Gewissheit, dass der Umzug wie geplant stattfindet' => 'Wir halten vereinbarte Termine ein, damit Sie sich voll auf Ihren Umzug verlassen können.',
        'Professionelles Management komplexer Logistik' => 'Auch anspruchsvolle Umzüge mit engen Zeitfenstern organisieren wir zuverlässig.',
        'Pünktlich, sorgfältig und ohne versteckte Kosten' => 'Unser Festpreis gilt verbindlich – ohne nachträgliche Überraschungen.',
        'Professionelle Verpackungsmaterialien und -techniken' => 'Hochwertiges Verpackungsmaterial schützt Ihr Hab und Gut auf dem gesamten Weg.',
        'Persönlicher Ansprechpartner für Ihren Umzug' => 'Sie haben während des gesamten Umzugs eine feste Kontaktperson an Ihrer Seite.',
        'Termine, die zu Ihrem Zeitplan passen' => 'Wir stimmen den Umzugstermin flexibel auf Ihre Bedürfnisse ab.',
        'Faire Festpreise, transparent kalkuliert' => 'Ihr Angebot zeigt jede Position einzeln – klar und nachvollziehbar.',
        'Rücksichtsvolles, geschultes Team' => 'Unsere Mitarbeiter gehen sorgfältig mit Ihrem Eigentum und Ihrer Wohnung um.',
        'Der passende Umzugsexperte für Sie' => 'Wir vermitteln den Umzugspartner, der am besten zu Ihrem Vorhaben passt.',
        'Termine rund um Ihren Zeitplan' => 'Ihr Umzugstermin richtet sich nach Ihrem Alltag, nicht umgekehrt.',
        'Faire Preise, transparent kalkuliert' => 'Sie erhalten ein Angebot, das jede Leistung einzeln aufschlüsselt.',
        'Freundlicher, aufmerksamer Service' => 'Von der ersten Anfrage an begleiten wir Sie zuvorkommend durch den Umzug.',
        'Klare Kommunikation ohne Fachjargon' => 'Wir erklären jeden Schritt verständlich, ohne komplizierte Fachbegriffe.',
        'Sorgfältige Planung jedes Schritts' => 'Jede Phase Ihres Büroumzugs wird im Voraus durchdacht und abgestimmt.',
        'Sensibler Umgang mit Technik & Akten' => 'Empfindliche Geräte und wichtige Unterlagen werden besonders geschützt transportiert.',
        'Minimale Ausfallzeiten für Ihr Team' => 'Wir planen den Umzug so, dass Ihr Betrieb so wenig wie möglich unterbrochen wird.',
        'Geprüfte Fachkräfte' => 'Jedes Teammitglied durchläuft eine gründliche Einarbeitung, bevor es bei Ihnen im Einsatz ist.',
        'Laufende Sicherheitsschulungen' => 'Regelmäßige Schulungen sorgen für sicheres Arbeiten bei jedem Umzug.',
        'Mehrsprachiger Support' => 'Unser Team steht Ihnen in mehreren Sprachen zur Verfügung.',
        'Kunde-zuerst-Mentalität' => 'Ihre Zufriedenheit hat für unser Team immer oberste Priorität.',
        'Günstige Umzugskosten bei hoher Servicequalität' => 'Faire Preise, ohne Abstriche bei Sorgfalt und Zuverlässigkeit.',
        'Persönliche Beratung und individuelle Planung' => 'Wir gehen auf Ihre individuellen Wünsche ein und planen den Umzug entsprechend.',
        'Zuverlässige und diskrete Fachpersonen' => 'Unser Team arbeitet diskret und respektvoll in Ihrem privaten Umfeld.',
        'Auch Umzüge ohne Ihre Anwesenheit möglich' => 'Auf Wunsch übernehmen wir den Umzug auch, wenn Sie selbst nicht vor Ort sein können.',
        'Transparente Preisgestaltung ohne versteckte Kosten' => 'Ihr Festpreis-Angebot zeigt alle Kosten offen und nachvollziehbar.',
    ];

    public function up(): void
    {
        foreach (self::FIELDS_BY_TYPE as $type => $fields) {
            $rows = DB::table('page_widget_translation')
                ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
                ->where('page_widgets.type', $type)
                ->select('page_widget_translation.id', 'page_widget_translation.data')
                ->get();

            foreach ($rows as $row) {
                $data = (array) json_decode((string) $row->data, true) ?: [];
                $changed = false;

                foreach ($fields as $field) {
                    if (! is_array($data[$field] ?? null)) {
                        continue;
                    }

                    $data[$field] = array_map(function ($item) use (&$changed) {
                        if (! is_array($item)) {
                            return $item;
                        }

                        $title = (string) ($item['title'] ?? '');
                        $hasDescription = ($item['description'] ?? '') !== '';

                        if (! $hasDescription && isset(self::DESCRIPTIONS[$title])) {
                            $item['description'] = self::DESCRIPTIONS[$title];
                            $changed = true;
                        }

                        return $item;
                    }, $data[$field]);
                }

                if ($changed) {
                    DB::table('page_widget_translation')->where('id', $row->id)->update([
                        'data' => json_encode($data),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (self::FIELDS_BY_TYPE as $type => $fields) {
            $rows = DB::table('page_widget_translation')
                ->join('page_widgets', 'page_widgets.id', '=', 'page_widget_translation.page_widget_id')
                ->where('page_widgets.type', $type)
                ->select('page_widget_translation.id', 'page_widget_translation.data')
                ->get();

            foreach ($rows as $row) {
                $data = (array) json_decode((string) $row->data, true) ?: [];
                $changed = false;

                foreach ($fields as $field) {
                    if (! is_array($data[$field] ?? null)) {
                        continue;
                    }

                    $data[$field] = array_map(function ($item) use (&$changed) {
                        if (! is_array($item)) {
                            return $item;
                        }

                        $title = (string) ($item['title'] ?? '');
                        $current = (string) ($item['description'] ?? '');

                        if (isset(self::DESCRIPTIONS[$title]) && $current === self::DESCRIPTIONS[$title]) {
                            $item['description'] = '';
                            $changed = true;
                        }

                        return $item;
                    }, $data[$field]);
                }

                if ($changed) {
                    DB::table('page_widget_translation')->where('id', $row->id)->update([
                        'data' => json_encode($data),
                    ]);
                }
            }
        }
    }
};
