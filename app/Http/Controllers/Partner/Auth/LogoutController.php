<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Logs the partner (company user) out of the `company` guard and
 * bounces them back to the login page.
 */
final class LogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::guard('company')->logout();

        $request->session()->regenerateToken();

        return redirect()->route('partner.login');
    }
}
