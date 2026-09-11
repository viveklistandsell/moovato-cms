<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\CompanyUser;
use App\Models\PartnerNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Partner-side notification management. Reads happen on the
 * dashboard controller — this controller only handles writes
 * (mark one read, mark all read).
 *
 * Ownership check is by company_user_id — a partner can only
 * touch their own notification rows, even if they guess an id.
 */
final class NotificationController extends Controller
{
    public function markRead(Request $request, PartnerNotification $notification): RedirectResponse
    {
        $user = $this->partner($request);

        if ((int) $notification->company_user_id !== (int) $user->id) {
            throw new NotFoundHttpException;
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $this->partner($request);

        PartnerNotification::query()
            ->forUser((int) $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return back();
    }

    private function partner(Request $request): CompanyUser
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');
        if ($user === null) {
            throw new NotFoundHttpException;
        }

        return $user;
    }
}
