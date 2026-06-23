<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CookieConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Single endpoint the cookie banner pings every time a visitor makes a
 * consent choice. Writes one pseudonymised row to `cookie_consents`.
 *
 * Public route — no auth, no CSRF token required because the banner
 * fires before any session is established. We protect it with light
 * server-side validation (rate-limit + closed allow-list of values) so
 * a scripted abuser can't flood the table.
 */
final class CookieConsentController extends Controller
{
    private const ALLOWED_ACTIONS = [
        CookieConsent::ACTION_ACCEPT_ALL,
        CookieConsent::ACTION_REJECT_ALL,
        CookieConsent::ACTION_CUSTOM,
        CookieConsent::ACTION_REVOKE,
    ];

    private const ALLOWED_CATEGORIES = ['necessary', 'analytics', 'marketing'];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'in:'.implode(',', self::ALLOWED_ACTIONS)],
            'categories' => ['present', 'array'],
            'categories.*' => ['string', 'in:'.implode(',', self::ALLOWED_CATEGORIES)],
            'locale' => ['nullable', 'string', 'max:5'],
            'banner_version' => ['nullable', 'string', 'max:16'],
        ]);

        CookieConsent::create([
            'anonymized_ip' => $this->anonymiseIp((string) $request->ip()),
            'user_agent_hash' => hash('sha256', (string) $request->userAgent()),
            'categories_accepted' => array_values(array_unique($data['categories'])),
            'action' => $data['action'],
            'banner_version' => $data['banner_version'] ?? 'v1',
            'locale' => $data['locale'] ?? null, 
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Strip the last octet of an IPv4 or the last 80 bits of an IPv6.
     * Same scheme Google Analytics uses. The result is no longer
     * personal data under EU case law (CJEU C-582/14), so the row is
     * safe to keep indefinitely.
     */
    private function anonymiseIp(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';

            return implode('.', $parts);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $expanded = inet_ntop(inet_pton($ip) ?: '');
            if ($expanded === false) {
                return '0.0.0.0';
            }
            $parts = explode(':', $expanded);
            // Keep first 3 groups (48 bits), zero the rest.
            return $parts[0].':'.($parts[1] ?? '0').':'.($parts[2] ?? '0').'::';
        }

        return '0.0.0.0';
    }
}
