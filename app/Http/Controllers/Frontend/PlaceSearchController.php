<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Widgets\CostCalculator\CostCalculatorWidget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Server-side proxy for the Google Places API (New) used by the moving cost
 * calculator. The API key never reaches the browser and every lookup is cached,
 * so repeated searches for the same town cost nothing.
 *
 * Public routes — no auth, rate limited in the route definition. `suggest`
 * feeds the type-ahead, `resolve` turns the picked place id into coordinates.
 */
final class PlaceSearchController extends Controller
{
    private const AUTOCOMPLETE_URL = 'https://places.googleapis.com/v1/places:autocomplete';

    private const DETAILS_URL = 'https://places.googleapis.com/v1/places/';

    private const SUGGEST_TTL = 86400;

    private const DETAILS_TTL = 2592000;

    public function suggest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        $key = $this->apiKey();

        if ($key === null) {
            return response()->json([
                'suggestions' => $this->offlineMatches($data['q']),
                'configured' => false,
            ]);
        }

        $query = mb_strtolower(mb_trim($data['q']));

        $suggestions = Cache::remember(
            'places:suggest:'.md5($query.'|'.app()->getLocale()),
            self::SUGGEST_TTL,
            fn (): array => $this->fetchSuggestions($key, $query),
        );

        return response()->json(['suggestions' => $suggestions, 'configured' => true]);
    }

    public function resolve(Request $request): JsonResponse
    {
        $data = $request->validate([
            'place_id' => ['required', 'string', 'max:255'],
        ]);

        $key = $this->apiKey();

        if ($key === null) {
            return response()->json(['place' => null], 404);
        }

        $place = Cache::remember(
            'places:details:'.md5($data['place_id']),
            self::DETAILS_TTL,
            fn (): ?array => $this->fetchPlace($key, $data['place_id']),
        );

        if ($place === null) {
            return response()->json(['place' => null], 404);
        }

        return response()->json(['place' => $place]);
    }

    /**
     * @return array<int, array{place_id: string, name: string}>
     */
    private function fetchSuggestions(string $key, string $query): array
    {
        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $key,
            'X-Goog-FieldMask' => 'suggestions.placePrediction.placeId,suggestions.placePrediction.text',
        ])
            ->timeout(6)
            ->post(self::AUTOCOMPLETE_URL, [
                'input' => $query,
                'includedRegionCodes' => config('services.google.places_regions'),
                'languageCode' => app()->getLocale(),
            ]);

        if ($response->failed()) {
            Log::warning('Google Places autocomplete failed.', [
                'status' => $response->status(),
                'body' => $response->json('error.message'),
            ]);

            return [];
        }

        $suggestions = [];

        foreach ($response->json('suggestions', []) as $suggestion) {
            $placeId = data_get($suggestion, 'placePrediction.placeId');
            $name = data_get($suggestion, 'placePrediction.text.text');

            if (is_string($placeId) && is_string($name)) {
                $suggestions[] = ['place_id' => $placeId, 'name' => $name];
            }
        }

        return $suggestions;
    }

    /**
     * @return array{place_id: string, name: string, lat: float, lng: float}|null
     */
    private function fetchPlace(string $key, string $placeId): ?array
    {
        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $key,
            'X-Goog-FieldMask' => 'id,formattedAddress,displayName,location',
        ])
            ->timeout(6)
            ->get(self::DETAILS_URL.$placeId, [
                'languageCode' => app()->getLocale(),
            ]);

        if ($response->failed()) {
            Log::warning('Google Places details failed.', [
                'status' => $response->status(),
                'body' => $response->json('error.message'),
            ]);

            return null;
        }

        $lat = $response->json('location.latitude');
        $lng = $response->json('location.longitude');

        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return null;
        }

        return [
            'place_id' => $placeId,
            'name' => (string) ($response->json('formattedAddress')
                ?? $response->json('displayName.text')
                ?? ''),
            'lat' => (float) $lat,
            'lng' => (float) $lng,
        ];
    }

    /**
     * Offline suggestions from the widget's built-in location list, so the
     * calculator keeps working before a Places key is configured. Matching is
     * diacritic-insensitive: "thuringen" finds "Thüringen".
     *
     * @return array<int, array{name: string, lat: float, lng: float}>
     */
    private function offlineMatches(string $query): array
    {
        $needle = $this->fold($query);

        if ($needle === '') {
            return [];
        }

        $matches = array_filter(
            CostCalculatorWidget::defaultLocations(),
            fn (array $place): bool => str_contains($this->fold($place['name']), $needle),
        );

        return array_slice(array_values($matches), 0, 8);
    }

    private function fold(string $value): string
    {
        return str_replace(
            ['ae', 'oe', 'ue'],
            ['a', 'o', 'u'],
            str_replace(
                ['ä', 'ö', 'ü', 'ß', 'é', 'è', 'á', 'à', 'í', 'ó', 'ú'],
                ['a', 'o', 'u', 'ss', 'e', 'e', 'a', 'a', 'i', 'o', 'u'],
                mb_strtolower(mb_trim($value))
            )
        );
    }

    private function apiKey(): ?string
    {
        $key = config('services.google.places_key');

        return is_string($key) && $key !== '' ? $key : null;
    }
}
