<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\SendTestMailRequest;
use App\Http\Requests\Admin\Settings\UpdateEmailSettingsRequest;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Admin-managed SMTP / mail configuration. Three endpoints:
 *
 *   edit()     — render the form pre-filled with the singleton row
 *                (password masked, never round-tripped to the browser).
 *   update()   — persist + bust cache; AppServiceProvider picks up the
 *                new config on the next request automatically.
 *   sendTest() — fires a real Mail::raw using the LIVE config, so the
 *                admin gets immediate proof the credentials work. Any
 *                exception is caught and surfaced in the response so
 *                the UI can show it inline instead of crashing.
 *
 * Permission: `settings.email`.
 */
final class EmailSettingsController extends Controller
{
    public function edit(): Response
    {
        $s = EmailSetting::current();

        return Inertia::render('admin/settings/Email', [
            'settings' => [
                'mail_transport' => $s->mail_transport ?? 'smtp',
                'mail_host' => $s->mail_host,
                'mail_port' => $s->mail_port,
                'mail_username' => $s->mail_username,
                'mail_password_set' => $s->mail_password !== null && $s->mail_password !== '',
                'mail_encryption' => $s->mail_encryption,
                'mail_from_address' => $s->mail_from_address,
                'mail_from_name' => $s->mail_from_name,
            ],
        ]);
    }

    public function update(UpdateEmailSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (! \array_key_exists('mail_password', $data) || $data['mail_password'] === null || $data['mail_password'] === '') {
            unset($data['mail_password']);
        }
        $s = EmailSetting::query()->orderBy('id')->first() ?? EmailSetting::query()->create([]);
        $s->fill($data)->save();

        EmailSetting::flush();

        return back()->with('success', 'Email configuration saved.');
    }

    public function sendTest(SendTestMailRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->refreshMailManagerFromDatabase();
        if ($error = $this->validateRuntimeMailConfig()) {
            return back()->with('test_mail_error', $error);
        }

        try {
            Mail::html($data['message'], function (Message $mail) use ($data): void {
                $mail->to($data['email'])->subject($data['subject']);
            });
        } catch (Throwable $e) {
            return back()->with(
                'test_mail_error',
                'Send failed: '.$e->getMessage(),
            );
        }

        return back()->with(
            'test_mail_success',
            'Test mail dispatched to '.$data['email'].'. Check /admin/system/email-log to confirm delivery.',
        );
    }

    /**
     * The mail manager caches resolved mailers; updating config alone
     * isn't enough mid-request. `purge()` drops cached instances so the
     * next Mail::raw rebuilds with the new credentials.
     *
     * Crucially we DON'T overwrite SMTP keys with null values from the
     * DB — that would null out whatever .env provided and crash the DSN
     * parser. Empty fields fall back to the existing config (which
     * was already patched in AppServiceProvider with the same skip-
     * if-empty rule).
     */
    private function refreshMailManagerFromDatabase(): void
    {
        $s = EmailSetting::current();

        $transport = $s->mail_transport ?: (string) config('mail.default');
        if ($s->mail_transport) {
            config(['mail.default' => $s->mail_transport]);
        }

        if ($s->mail_host !== null && $s->mail_host !== '') {
            config(['mail.mailers.smtp.host' => $s->mail_host]);
        }
        if ($s->mail_port !== null && (int) $s->mail_port > 0) {
            config(['mail.mailers.smtp.port' => (int) $s->mail_port]);
        }
        if ($s->mail_username !== null && $s->mail_username !== '') {
            config(['mail.mailers.smtp.username' => $s->mail_username]);
        }
        if ($s->mail_password !== null && $s->mail_password !== '') {
            config(['mail.mailers.smtp.password' => $s->mail_password]);
        }
        // Encryption is allowed to be null (= no encryption); persist it.
        config(['mail.mailers.smtp.encryption' => $s->mail_encryption ?: null]);

        if ($s->mail_from_address !== null && $s->mail_from_address !== '') {
            config(['mail.from.address' => $s->mail_from_address]);
        }
        if ($s->mail_from_name !== null && $s->mail_from_name !== '') {
            config(['mail.from.name' => $s->mail_from_name]);
        }

        app('mail.manager')->purge($transport);
    }
    private function validateRuntimeMailConfig(): ?string
    {
        $transport = (string) config('mail.default');

        if ($transport === 'smtp') {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');

            if ($host === null || $host === '') {
                return 'Send failed: SMTP host is not configured. Fill "Mail Host" and click Save Changes first, then try the test again.';
            }
            if ($port === null || (int) $port <= 0) {
                return 'Send failed: SMTP port is not configured. Fill "Mail Port" and click Save Changes first.';
            }
        }

        $from = config('mail.from.address');
        if ($from === null || $from === '') {
            return 'Send failed: "From Address" is not configured. Fill it in and click Save Changes first.';
        }

        return null;
    }
}
