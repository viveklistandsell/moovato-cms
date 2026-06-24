<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Persists the authenticated admin's preferred UI language. Bound to
 * the header switcher's PATCH endpoint. Validates against the active
 * Language table so a malicious client can't set an arbitrary locale
 * code that would later break translation lookups.
 */
final class AdminLocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $activeCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        $data = $request->validate([
            'locale' => ['required', 'string', 'max:5', 'in:'.implode(',', $activeCodes)],
        ]);

        $user = $request->user();
        if ($user !== null) {
            $user->forceFill(['admin_locale' => $data['locale']])->save();
        }

        return back();
    }
}
