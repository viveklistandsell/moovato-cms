<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin flips a review between published / hidden / spam. Hidden and
 * spam reviews stay in the DB (never delete customer-generated content
 * on a status change) but are excluded from the frontend and from the
 * observer's cache aggregates.
 */
final class UpdateReviewStatusRequest extends FormRequest
{
    public const ALLOWED_STATUSES = ['published', 'hidden', 'spam'];

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
            'status' => ['required', Rule::in(self::ALLOWED_STATUSES)],
        ];
    }
}
