<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

/**
 * Idempotent seed for the single email_settings row. Seeds initial values
 * from the current .env so a fresh install matches what was already
 * working, and the admin can edit from there.
 */
final class EmailSettingSeeder extends Seeder
{
    public function run(): void
    {
        $s = EmailSetting::query()->orderBy('id')->first() ?? new EmailSetting;

        $s->fill([
            'mail_transport' => (string) env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT') !== null ? (int) env('MAIL_PORT') : null,
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME'),
        ])->save();

        EmailSetting::flush();
    }
}
