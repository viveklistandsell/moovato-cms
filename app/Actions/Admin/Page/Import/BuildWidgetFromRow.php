<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import;

use App\Actions\Admin\Page\Import\Support\ResolveMedia;
use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Widgets\Registry\WidgetRegistry;

/**
 * Turns one section row from the import CSV into the widget-row shape
 * that {@see SyncPageWidgets} already
 * expects: `{type, settings, translations: {lang: data}}`.
 *
 * Three input shapes are supported, in this order:
 *
 *   1. `section_json` column (JSON template format) — the cell is a JSON
 *      object that carries every field (widget_type, title, description,
 *      image, image_alt, columns, service_items, expertise_items, …).
 *      When present it wins; the flat columns are ignored.
 *
 *   2. Aliased slug in `sections` (`service_hero`, `content_image`, …) —
 *      resolved via {@see WidgetAliasMap} to a Moovato widget type with
 *      pinned settings (e.g. `image_side='left'` for `zig_zag_content`).
 *
 *   3. Native Moovato widget slug in `sections` (`hero`, `features`, …)
 *      — anything registered in {@see WidgetRegistry}. Field routing
 *      is generic: `title` cell fills the first title-ish key in the
 *      widget's `defaultData()`, `description` fills the first body-ish
 *      key, and so on. Unknown widgets return null so the importer can
 *      skip and warn.
 */
final readonly class BuildWidgetFromRow
{
    /**
     * Data-key priority list for the CSV `title` cell. First key that
     * exists in the widget's defaultData() wins.
     *
     * @var list<string>
     */
    private const TITLE_KEYS = ['title', 'heading', 'title_lead', 'title_highlight', 'name'];

    /**
     * Data-key priority list for the CSV `description` cell.
     *
     * @var list<string>
     */
    private const BODY_KEYS = ['subtitle', 'body', 'subheading', 'description', 'content'];

    public function __construct(
        private WidgetAliasMap $aliases,
        private WidgetRegistry $registry,
        private ResolveMedia $media,
    ) {}

    /**
     * Convert HTML-flavoured cell content to the plain-text form the widget
     * renderers actually display. Widget templates use `{{ data.body }}`
     * (escaped interpolation), so a raw `<p>Hello</p>` would render as
     * literal text on the frontend. We normalise here once, at import time,
     * so what's stored matches what the editor produces.
     *
     * Rules:
     *   - `<br>` and `</p>` become real newlines (paragraph structure kept).
     *   - `<li>` becomes a newline + "• " so leftover list items still read.
     *   - Remaining tags are stripped.
     *   - HTML entities (`&auml;`, `&amp;`, `&nbsp;`, …) are decoded.
     *   - Runs of 3+ blank lines are collapsed to two; leading/trailing
     *     whitespace is trimmed.
     */
    public static function toPlainText(string $raw): string
    {
        if ($raw === '') {
            return '';
        }

        $s = preg_replace('#<\s*br\s*/?\s*>#i', "\n", $raw) ?? $raw;
        $s = preg_replace('#</\s*(p|div|section|article|h[1-6])\s*>#i', "\n\n", $s) ?? $s;
        $s = preg_replace('#<\s*li[^>]*>#i', "\n• ", $s) ?? $s;
        $s = strip_tags($s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = preg_replace("#[ \t]+\n#", "\n", $s) ?? $s;
        $s = preg_replace("#\n{3,}#", "\n\n", $s) ?? $s;

        return mb_trim($s);
    }

    /**
     * @param  array<string, string>  $row  normalised CSV row (see ImportPagesFromCsv::associate)
     * @return array{type: string, settings: array<string, mixed>, translations: array<string, array<string, mixed>>}|null
     */
    public function build(array $row, string $lang): ?array
    {
        $jsonRaw = mb_trim($row['section_json'] ?? '');
        if ($jsonRaw !== '') {
            $decoded = json_decode($jsonRaw, true);
            if (is_array($decoded)) {
                if (isset($decoded['widget_type']) && is_string($decoded['widget_type'])) {
                    $row['sections'] = $decoded['widget_type'];
                }
                foreach ($decoded as $k => $v) {
                    if ($k === 'widget_type') {
                        continue;
                    }
                    if (is_scalar($v) || $v === null) {
                        $row[$k] = (string) ($v ?? '');
                    } else {
                        $row[$k] = json_encode($v, JSON_UNESCAPED_UNICODE) ?: '';
                    }
                }
            }
        }

        $csvSlug = mb_strtolower(mb_trim($row['sections'] ?? ''));
        if ($csvSlug === '') {
            return null;
        }

        [$type, $settingsOverrides] = $this->resolveType($csvSlug);
        if ($type === null || ! $this->registry->has($type)) {
            return null;
        }

        $definition = $this->registry->resolve($type);
        $settings = array_merge($definition::defaultSettings(), $settingsOverrides);
        $data = $this->stripDefaultCopy($type, $definition::defaultData());

        $imagePath = $this->media->pathFor($row['image'] ?? null);
        $imageAlt = mb_trim($row['image_alt'] ?? '');
        $title = mb_trim($row['title'] ?? '');
        $description = (string) ($row['description'] ?? '');

        if ($imagePath !== null) {
            if (array_key_exists('image_path', $settings)) {
                $settings['image_path'] = $imagePath;
            }
            if (array_key_exists('image_url', $settings)) {
                $settings['image_url'] = '/storage/'.mb_ltrim($imagePath, '/');
            }
        }
        if ($imageAlt !== '' && array_key_exists('image_alt', $data)) {
            $data['image_alt'] = $imageAlt;
        }

        switch ($type) {
            case 'faq':
                if ($title !== '' && ! str_contains($title, '||')) {
                    $data['heading'] = self::toPlainText($title);
                }
                $data['items'] = $this->buildFaqItems($title, $description, $row);
                break;

            case 'features':
                if ($title !== '') {
                    $data['heading'] = self::toPlainText($title);
                }
                if ($description !== '') {
                    $data['subheading'] = self::toPlainText($description);
                }
                $data['items'] = $this->buildItemsFromJson($row['service_items'] ?? $row['expertise_items'] ?? $row['process_items'] ?? '', ['title', 'description', 'icon']);
                if (isset($row['columns']) && $row['columns'] !== '' && array_key_exists('columns', $settings)) {
                    $settings['columns'] = (int) $row['columns'];
                }
                break;

            default:
                $this->routeTitleAndBody($data, $type, $title, $description);
                if (array_key_exists('points', $data)) {
                    [$body, $points] = $this->splitBodyAndListItems($description);
                    if ($body !== '' && array_key_exists('body', $data)) {
                        $data['body'] = self::toPlainText($body);
                    }
                    if ($points !== []) {
                        $data['points'] = $points;
                    }
                }
                break;
        }

        return [
            'type' => $type,
            'settings' => $settings,
            'translations' => [$lang => $data],
        ];
    }

    /**
     * @return array{0: string|null, 1: array<string, mixed>}
     */
    private function resolveType(string $csvSlug): array
    {
        $alias = $this->aliases->resolve($csvSlug);
        if ($alias !== null) {
            return [$alias['type'], $alias['settings_overrides']];
        }

        if ($this->registry->has($csvSlug)) {
            return [$csvSlug, []];
        }

        return [null, []];
    }

    /**
     * Place `title` into the first title-ish key that exists on the widget,
     * `description` into the first body-ish key. Widgets whose fields don't
     * follow either convention (rare) are left untouched — the CSV editor
     * can still fill them via `section_json`.
     *
     * @param  array<string, mixed>  $data
     */
    private function routeTitleAndBody(array &$data, string $type, string $title, string $description): void
    {
        if ($title !== '') {
            $titleText = self::toPlainText($title);
            foreach (self::TITLE_KEYS as $key) {
                if (array_key_exists($key, $data)) {
                    $data[$key] = $titleText;
                    break;
                }
            }
        }
        if ($description !== '') {
            $bodyText = self::toPlainText($description);
            foreach (self::BODY_KEYS as $key) {
                if (array_key_exists($key, $data)) {
                    $data[$key] = $bodyText;
                    break;
                }
            }
        }
        unset($type);
    }

    /**
     * Widgets with repeater lists ship with demo entries in defaultData().
     * Clear them so imported rows start from CSV content rather than
     * inheriting placeholder copy.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function stripDefaultCopy(string $type, array $data): array
    {
        foreach (['items', 'points', 'features', 'pills', 'avatars'] as $listKey) {
            if (array_key_exists($listKey, $data) && is_array($data[$listKey])) {
                $data[$listKey] = [];
            }
        }
        foreach (self::BODY_KEYS as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '';
            }
        }
        unset($type);

        return $data;
    }

    /**
     * FAQ rows can arrive in two shapes:
     *   1. Flat CSV — `title` = "Q1||Q2", `description` = "A1||A2"
     *   2. JSON     — `items` (from section_json) already an array of {question, answer}
     *
     * @param  array<string, string>  $row
     * @return list<array{question: string, answer: string}>
     */
    private function buildFaqItems(string $questionsRaw, string $answersRaw, array $row): array
    {
        if (isset($row['items']) && mb_trim((string) $row['items']) !== '') {
            $decoded = json_decode((string) $row['items'], true);
            if (is_array($decoded)) {
                $out = [];
                foreach ($decoded as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $q = self::toPlainText((string) ($item['question'] ?? $item['q'] ?? ''));
                    $a = self::toPlainText((string) ($item['answer'] ?? $item['a'] ?? ''));
                    if ($q === '' && $a === '') {
                        continue;
                    }
                    $out[] = ['question' => $q, 'answer' => $a];
                }

                return $out;
            }
        }

        $questions = array_map('mb_trim', explode('||', $questionsRaw));
        $answers = array_map('mb_trim', explode('||', $answersRaw));

        $items = [];
        $count = max(count($questions), count($answers));
        for ($i = 0; $i < $count; $i++) {
            $q = self::toPlainText($questions[$i] ?? '');
            $a = self::toPlainText($answers[$i] ?? '');
            if ($q === '' && $a === '') {
                continue;
            }
            $items[] = ['question' => $q, 'answer' => $a];
        }

        return $items;
    }

    /**
     * Decode a JSON array cell into a list of items with just the allowed
     * keys kept (drops anything the widget didn't ask for).
     *
     * @param  list<string>  $allowedKeys
     * @return list<array<string, string>>
     */
    private function buildItemsFromJson(string $rawJson, array $allowedKeys): array
    {
        $raw = mb_trim($rawJson);
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            $entry = [];
            foreach ($allowedKeys as $k) {
                if (array_key_exists($k, $item)) {
                    $value = (string) ($item[$k] ?? '');
                    $entry[$k] = $k === 'icon' ? $value : self::toPlainText($value);
                }
            }
            if ($entry !== []) {
                $out[] = $entry;
            }
        }

        return $out;
    }

    /**
     * Split a description cell that mixes prose and a list. The prose becomes
     * `data.body`; the `<li>` entries become `data.points`.
     *
     * @return array{0: string, 1: list<string>}
     */
    private function splitBodyAndListItems(string $html): array
    {
        $points = [];
        if (preg_match_all('#<li[^>]*>(.+?)</li>#is', $html, $matches) > 0) {
            foreach ($matches[1] as $item) {
                $text = self::toPlainText((string) $item);
                if ($text !== '') {
                    $points[] = $text;
                }
            }
        }

        $body = preg_replace('#<ul[^>]*>.*?</ul>#is', '', $html) ?? $html;
        $body = mb_trim($body);

        return [$body, $points];
    }
}
