<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Reviews;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates an admin reply on a customer review. Reply content lives
 * inline on the review row (reply_body, replied_at, reply_by_user_id) —
 * one reply per review, matching Google Reviews UX.
 *
 * The `authorize()` check is skipped here because the route already
 * requires the `companies.view` permission; access has been decided
 * before this request ever runs.
 */
final class StoreReplyRequest extends FormRequest
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
            'reply_body' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }
}
