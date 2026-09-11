<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\CompanyApplications;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the rejection reason an admin types before clicking
 * "Confirm reject". Reason is surfaced back to the applicant in the
 * rejection email so they know what went wrong.
 *
 * `authorize()` is true — the route already requires the
 * `companies.view` permission; access is decided BEFORE this form
 * request ever runs.
 */
final class RejectApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }
}
