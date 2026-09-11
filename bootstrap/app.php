<?php

declare(strict_types=1);

use App\Http\Middleware\EnsurePartnerOwnsCompany;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\SetAdminLocale;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->preventRequestForgery(except: ['cookie-consent']);

        $middleware->web(append: [
            MaintenanceMode::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'locale' => SetLocale::class,
            'admin' => EnsureUserIsAdmin::class,
            'admin.locale' => SetAdminLocale::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'partner.owns.company' => EnsurePartnerOwnsCompany::class,
        ]);

        // Where `auth:<guard>` sends anonymous visitors.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('company-portal/*') || $request->is('partner/*')) {
                return route('partner.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('partner/*') || $request->is('company-portal/*')) {
                return route('partner.dashboard');
            }

            return '/dashboard';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request): Response {
            if (! in_array($response->getStatusCode(), [404, 405], true)) {
                return $response;
            }
            if ($request->expectsJson() || $request->is('api/*')) {
                return $response;
            }
            if ($request->is('admin/*') || $request->is('admin')) {
                return $response;
            }

            return Inertia::render('frontend/NotFound', [
                'locale' => app()->getLocale(),
            ])->toResponse($request)->setStatusCode(404);
        });
    })->create();
