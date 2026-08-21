<?php

declare(strict_types=1);

namespace App\Http\Requests\Partner\Reviews;

use Illuminate\Foundation\Http\FormRequest;

final class StorePartnerReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('company') !== null;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'reply_body' => ['required', 'string', 'min:1', 'max:5000'],
        ];
    }
}
