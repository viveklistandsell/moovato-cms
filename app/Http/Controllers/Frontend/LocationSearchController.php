<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class LocationSearchController extends Controller
{
    private const MAX_TOTAL = 12;

    private const MAX_PER_KIND = 4;

    /**
     * Autocomplete backend for the CitySearch widget.
     *
     * @return JsonResponse{results: list<array{kind: string, id: int, name: string, subtitle: string, url: string}>}
     */
    public function suggest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $needle = mb_strtolower(mb_trim($data['q']));
        $like = '%'.self::escapeLike($needle).'%';

        $locale = App::getLocale();

        $results = [
            ...$this->matchCities($like, $locale),
            ...$this->matchDistricts($like, $locale),
            ...$this->matchStates($like, $locale),
            ...$this->matchCountries($like, $locale),
        ];

        $ranked = collect($results)
            ->sortBy(fn (array $r): int => str_starts_with(mb_strtolower($r['name']), $needle) ? 0 : 1)
            ->values()
            ->take(self::MAX_TOTAL)
            ->all();

        return response()->json(['results' => $ranked]);
    }

    public function resolve(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:city,district,state,country'],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $url = $this->urlFor((string) $data['kind'], (int) $data['id']);
        if ($url === null) {
            throw new NotFoundHttpException;
        }

        return redirect($url);
    }

    public function redirect(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $needle = mb_strtolower(mb_trim($data['q']));
        $like = '%'.self::escapeLike($needle).'%';
        $locale = App::getLocale();

        $results = [
            ...$this->matchCities($like, $locale),
            ...$this->matchDistricts($like, $locale),
            ...$this->matchStates($like, $locale),
            ...$this->matchCountries($like, $locale),
        ];

        $top = collect($results)
            ->sortBy(function (array $r) use ($needle): int {
                $name = mb_strtolower($r['name']);
                if ($name === $needle) {
                    return 0;
                }
                if (str_starts_with($name, $needle)) {
                    return 1;
                }

                return 2;
            })
            ->first();

        if ($top !== null) {
            return redirect((string) $top['url']);
        }

        return redirect($this->localizedPath($locale, '/companies?q='.urlencode($data['q'])));
    }

    /**
     * Escape the SQL LIKE metacharacters so a user typing "50%" doesn't
     * match every row that contains a digit.
     */
    private static function escapeLike(string $s): string
    {
        return addcslashes($s, '\_%');
    }

    /**
     * @return list<array{kind: string, id: int, name: string, subtitle: string, url: string}>
     */
    private function matchCities(string $like, string $locale): array
    {
        return City::query()
            ->where('status', 'published')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(self::MAX_PER_KIND)
            ->with('state:id,name,country_id')
            ->get(['id', 'name', 'state_id'])
            ->map(fn (City $c): array => [
                'kind' => 'city',
                'id' => (int) $c->id,
                'name' => (string) $c->name,
                'subtitle' => (string) ($c->state->name ?? __('search.kind_city')),
                'url' => $this->localizedPath($locale, '/companies?city='.$c->id),
            ])
            ->all();
    }

    /**
     * @return list<array{kind: string, id: int, name: string, subtitle: string, url: string}>
     */
    private function matchDistricts(string $like, string $locale): array
    {
        return District::query()
            ->where('status', 'published')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(self::MAX_PER_KIND)
            ->with('city:id,name')
            ->get(['id', 'name', 'city_id'])
            ->map(fn (District $d): array => [
                'kind' => 'district',
                'id' => (int) $d->id,
                'name' => (string) $d->name,
                'subtitle' => (string) ($d->city->name ?? __('search.kind_district')),
                'url' => $this->localizedPath($locale, '/companies?district='.$d->id),
            ])
            ->all();
    }

    /**
     * @return list<array{kind: string, id: int, name: string, subtitle: string, url: string}>
     */
    private function matchStates(string $like, string $locale): array
    {
        return State::query()
            ->where('status', 'published')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(self::MAX_PER_KIND)
            ->with('country:id,name')
            ->get(['id', 'name', 'country_id'])
            ->map(fn (State $s): array => [
                'kind' => 'state',
                'id' => (int) $s->id,
                'name' => (string) $s->name,
                'subtitle' => (string) ($s->country->name ?? __('search.kind_state')),
                'url' => $this->localizedPath($locale, '/companies?state='.$s->id),
            ])
            ->all();
    }

    /**
     * @return list<array{kind: string, id: int, name: string, subtitle: string, url: string}>
     */
    private function matchCountries(string $like, string $locale): array
    {
        return Country::query()
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(self::MAX_PER_KIND)
            ->get(['id', 'name', 'iso_code'])
            ->map(fn (Country $c): array => [
                'kind' => 'country',
                'id' => (int) $c->id,
                'name' => (string) $c->name,
                'subtitle' => __('search.kind_country'),
                'url' => $this->localizedPath($locale, '/companies?country='.$c->id),
            ])
            ->all();
    }

    private function urlFor(string $kind, int $id): ?string
    {
        $locale = App::getLocale();
        $exists = match ($kind) {
            'city' => City::query()->whereKey($id)->where('status', 'published')->exists(),
            'district' => District::query()->whereKey($id)->where('status', 'published')->exists(),
            'state' => State::query()->whereKey($id)->where('status', 'published')->exists(),
            'country' => Country::query()->whereKey($id)->exists(),
            default => false,
        };
        if (! $exists) {
            return null;
        }

        return $this->localizedPath($locale, '/companies?'.$kind.'='.$id);
    }

    private function localizedPath(string $locale, string $path): string
    {
        $default = config('app.locale');
        $normalized = str_starts_with($path, '/') ? $path : '/'.$path;
        if ($locale === $default) {
            return $normalized;
        }

        return '/'.$locale.$normalized;
    }
}
