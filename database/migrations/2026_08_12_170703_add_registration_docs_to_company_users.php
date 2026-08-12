<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registration documents uploaded during the /partner/register form.
 *
 * Stored inline on the CompanyUser as an array of {name, path}
 * entries instead of the CompanyMedia pivot because these docs are:
 *   - Private (never shown on the public profile)
 *   - Bound to the APPLICATION, not the company (approval or
 *     rejection cleans them up together with the row)
 *   - Never re-ordered or edited by admin
 *
 * Files land in `storage/app/private/partner-registration-docs/`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_users', function (Blueprint $t): void {
            $t->json('registration_docs')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('company_users', function (Blueprint $t): void {
            $t->dropColumn('registration_docs');
        });
    }
};
