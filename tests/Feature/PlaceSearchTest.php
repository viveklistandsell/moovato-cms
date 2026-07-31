<?php

declare(strict_types=1);

use App\Http\Controllers\Frontend\PlaceSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

function placeRequest(array $query): Request
{
    return Request::create('/places', 'GET', $query);
}

test('suggest falls back to the built-in list when places is not configured', function (): void {
    config()->set('services.google.places_key', null);
    Http::fake();

    $payload = (new PlaceSearchController())
        ->suggest(placeRequest(['q' => 'Dachau']))
        ->getData(true);

    expect($payload['configured'])->toBeFalse()
        ->and($payload['suggestions'][0])->toBe([
            'name' => 'Dachau, Deutschland',
            'lat' => 48.2603,
            'lng' => 11.4342,
        ]);

    Http::assertNothingSent();
});

test('the offline fallback ignores umlauts so thuringen finds Thüringen', function (): void {
    config()->set('services.google.places_key', null);

    $payload = (new PlaceSearchController())
        ->suggest(placeRequest(['q' => 'thuringen']))
        ->getData(true);

    expect($payload['suggestions'])->toHaveCount(1)
        ->and($payload['suggestions'][0]['name'])->toBe('Thüringen, Deutschland');
});

test('suggest proxies google autocomplete and maps the predictions', function (): void {
    config()->set('services.google.places_key', 'test-key');
    config()->set('services.google.places_regions', ['de']);

    Http::fake([
        'places.googleapis.com/v1/places:autocomplete' => Http::response([
            'suggestions' => [
                [
                    'placePrediction' => [
                        'placeId' => 'place-dachau',
                        'text' => ['text' => 'Dachau, Deutschland'],
                    ],
                ],
                ['placePrediction' => ['placeId' => 'no-text']],
            ],
        ]),
    ]);

    $response = (new PlaceSearchController())->suggest(placeRequest(['q' => 'Dachau']));

    expect($response->getData(true))->toBe([
        'suggestions' => [
            ['place_id' => 'place-dachau', 'name' => 'Dachau, Deutschland'],
        ],
        'configured' => true,
    ]);

    Http::assertSent(fn ($request) => $request['input'] === 'dachau'
        && $request['includedRegionCodes'] === ['de']
        && $request->hasHeader('X-Goog-Api-Key', 'test-key'));
});

test('suggest rejects a query that is too short', function (): void {
    config()->set('services.google.places_key', 'test-key');

    (new PlaceSearchController())->suggest(placeRequest(['q' => 'D']));
})->throws(ValidationException::class);

test('resolve turns a place id into coordinates', function (): void {
    config()->set('services.google.places_key', 'test-key');

    Http::fake([
        'places.googleapis.com/v1/places/place-thueringen*' => Http::response([
            'id' => 'place-thueringen',
            'formattedAddress' => 'Thüringen, Deutschland',
            'location' => ['latitude' => 50.9013, 'longitude' => 11.0177],
        ]),
    ]);

    $response = (new PlaceSearchController())->resolve(
        placeRequest(['place_id' => 'place-thueringen'])
    );

    expect($response->getData(true)['place'])->toBe([
        'place_id' => 'place-thueringen',
        'name' => 'Thüringen, Deutschland',
        'lat' => 50.9013,
        'lng' => 11.0177,
    ]);
});

test('resolve returns 404 when google has no usable coordinates', function (): void {
    config()->set('services.google.places_key', 'test-key');

    Http::fake([
        'places.googleapis.com/*' => Http::response(['error' => ['message' => 'nope']], 400),
    ]);

    $response = (new PlaceSearchController())->resolve(placeRequest(['place_id' => 'broken']));

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true))->toBe(['place' => null]);
});

test('resolve returns 404 when places is not configured', function (): void {
    config()->set('services.google.places_key', null);
    Http::fake();

    $response = (new PlaceSearchController())->resolve(placeRequest(['place_id' => 'anything']));

    expect($response->getStatusCode())->toBe(404);
    Http::assertNothingSent();
});
