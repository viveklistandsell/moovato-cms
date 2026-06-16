<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only viewer for the outgoing email log. Index is paginated with
 * status / actor / date / free-text filters. Show streams the full body
 * (HTML + text + headers) so an admin can verify what actually went out
 * the door.
 */
final class EmailLogController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $triggeredBy = $request->query('triggered_by');
        $search = mb_trim((string) $request->query('q', ''));

        $query = EmailLog::query()
            ->with('triggeredBy:id,name,email')->latest();

        if (is_string($status) && in_array($status, ['pending', 'sent', 'failed'], true)) {
            $query->where('status', $status);
        }

        if (is_numeric($triggeredBy)) {
            $query->where('triggered_by_user_id', (int) $triggeredBy);
        }

        if ($search !== '') {
            $like = sprintf('%%%s%%', $search);
            $query->where(function (Builder $q) use ($like): void {
                $q->where('subject', 'like', $like)
                    ->orWhere('from_address', 'like', $like)
                    ->orWhere('to_addresses', 'like', $like)
                    ->orWhere('cc_addresses', 'like', $like);
            });
        }

        $paginator = $query->paginate(25)->withQueryString();

        return Inertia::render('admin/system/EmailLog', [
            'logs' => [
                'data' => $paginator->getCollection()
                    ->map(fn (EmailLog $l): array => $this->presentRow($l))
                    ->all(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
            'filters' => [
                'status' => $status,
                'triggered_by' => is_numeric($triggeredBy) ? (int) $triggeredBy : null,
                'q' => $search,
            ],
            'options' => [
                'actors' => User::query()
                    ->whereIn(
                        'id',
                        EmailLog::query()->distinct()->pluck('triggered_by_user_id')->filter(),
                    )
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (User $u): array => ['id' => $u->id, 'name' => $u->name])
                    ->all(),
            ],
        ]);
    }

    public function show(EmailLog $emailLog): Response
    {
        $emailLog->load('triggeredBy:id,name,email');

        return Inertia::render('admin/system/EmailLogShow', [
            'log' => [
                ...$this->presentRow($emailLog),
                'body_html' => $emailLog->body_html,
                'body_text' => $emailLog->body_text,
                'headers' => $emailLog->headers ?? [],
                'cc_addresses' => $emailLog->cc_addresses,
                'bcc_addresses' => $emailLog->bcc_addresses,
                'reply_to' => $emailLog->reply_to,
                'attachment_count' => $emailLog->attachment_count,
                'message_id' => $emailLog->message_id,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentRow(EmailLog $l): array
    {
        return [
            'id' => $l->id,
            'mailer' => $l->mailer,
            'from_address' => $l->from_address,
            'from_name' => $l->from_name,
            'to_addresses' => $l->to_addresses,
            'subject' => $l->subject,
            'status' => $l->status,
            'error' => $l->error,
            'attachment_count' => $l->attachment_count,
            'created_at' => $l->created_at?->toIso8601String(),
            'sent_at' => $l->sent_at?->toIso8601String(),
            'triggered_by' => $l->triggeredBy
                ? ['id' => $l->triggeredBy->id, 'name' => $l->triggeredBy->name, 'email' => $l->triggeredBy->email]
                : null,
        ];
    }
}
