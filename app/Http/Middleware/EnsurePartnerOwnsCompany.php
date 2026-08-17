<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\CompanyUser;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Ownership gate for the partner-facing portal edit endpoints.
 *
 * Assumes `auth:company` has already run in front of it — this
 * middleware only enforces the additional check that the currently
 * authenticated partner OWNS the `{company}` route parameter.
 *
 * Ownership = the CompanyUser's `company_id` matches the route
 * parameter's id. A partner may only ever be linked to one company
 * so the check is a simple integer equality.
 *
 * Anyone else (approved partner from a different company, admin
 * session bleeding through the `web` guard, etc.) gets a 404 —
 * NOT 403 — so we don't disclose that the target company exists.
 */
final class EnsurePartnerOwnsCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');

        if ($user === null) {
            throw new NotFoundHttpException;
        }

        $routeCompany = $request->route('company');
        if ($routeCompany instanceof Model) {
            $companyId = (int) $routeCompany->getKey();
        } elseif (is_numeric($routeCompany)) {
            $companyId = (int) $routeCompany;
        } else {
            throw new NotFoundHttpException;
        }

        if ($companyId <= 0 || (int) $user->company_id !== $companyId) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
