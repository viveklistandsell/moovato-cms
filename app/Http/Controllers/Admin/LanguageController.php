<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Language\StoreLanguageRequest;
use App\Http\Requests\Admin\Language\UpdateLanguageRequest;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class LanguageController
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'code' => 'code',
        'name' => 'name',
        'status' => 'status',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = Language::query();

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('native_name', 'like', $like)
                    ->orWhere('code', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderBy('sort_order');
        }

        $languages = $query->get()
            ->map(fn (Language $l): array => $this->present($l))
            ->values()
            ->all();

        return Inertia::render('admin/languages/Index', [
            'languages' => $languages,
            'filters' => [
                'q' => $search,
                'sort_by' => is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS) ? $sortBy : null,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/languages/Edit', [
            'language' => null,
            'nextSortOrder' => Language::nextSortOrder(),
        ]);
    }

    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            $language = Language::query()->create([
                'code' => $data['code'],
                'name' => $data['name'],
                'native_name' => $data['native_name'],
                'flag' => $data['flag'] ?? null,
                'lang_locale' => $data['lang_locale'] ?? null,
                'lang_is_default' => $data['lang_is_default'] ?? false,
                'status' => $data['status'] ?? true,
                'sort_order' => $data['sort_order'] ?? Language::nextSortOrder(),
            ]);

            if (($data['lang_is_default'] ?? false) === true) {
                Language::clearOtherDefaults($language->id);
            }
        });

        $this->forgetCaches();

        return redirect()
            ->route('admin.languages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Language added.']);
    }

    public function edit(Language $language): Response
    {
        return Inertia::render('admin/languages/Edit', [
            'language' => $this->present($language),
            'nextSortOrder' => Language::nextSortOrder(),
        ]);
    }

    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        DB::transaction(function () use ($language, $data): void {
            $language->fill([
                'code' => $data['code'],
                'name' => $data['name'],
                'native_name' => $data['native_name'],
                'flag' => $data['flag'] ?? null,
                'lang_locale' => $data['lang_locale'] ?? null,
                'lang_is_default' => $data['lang_is_default'] ?? false,
                'status' => $data['status'] ?? true,
                'sort_order' => $data['sort_order'] ?? $language->sort_order,
            ]);
            $language->save();

            if (($data['lang_is_default'] ?? false) === true) {
                Language::clearOtherDefaults($language->id);
            }
        });

        $this->forgetCaches();

        return redirect()
            ->route('admin.languages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Language updated.']);
    }

    public function destroy(Language $language): RedirectResponse
    {
        if ($language->lang_is_default) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Cannot delete the default language. Set another as default first.',
            ]);
        }

        $language->delete();
        $this->forgetCaches();

        return redirect()
            ->route('admin.languages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Language removed.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Language $language): array
    {
        return [
            'id' => $language->id,
            'code' => $language->code,
            'name' => $language->name,
            'native_name' => $language->native_name,
            'flag' => $language->flag,
            'lang_locale' => $language->lang_locale,
            'lang_is_default' => $language->lang_is_default,
            'status' => $language->status,
            'sort_order' => $language->sort_order,
            'created_at' => $language->created_at?->toIso8601String(),
            'updated_at' => $language->updated_at?->toIso8601String(),
        ];
    }

    private function forgetCaches(): void
    {
        Cache::forget('locales.active.codes');
        Cache::forget('locales.default.code');
    }
}
