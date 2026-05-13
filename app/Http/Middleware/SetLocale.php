<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        $availableCodes = $this->availableCodes();
        $defaultCode = $this->defaultCode();

        if (! is_string($locale) || ! in_array($locale, $availableCodes, true)) {
            $locale = $defaultCode;
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * @return array<int, string>
     */
    private function availableCodes(): array
    {
        return Cache::remember('locales.active.codes', 300, fn () => Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->all());
    }

    private function defaultCode(): string
    {
        return Cache::remember(
            'locales.default.code',
            300,
            fn () => Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? config('app.locale'),
        );
    }
}
