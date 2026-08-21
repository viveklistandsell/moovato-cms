<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\PlanChangeRequests;

use Illuminate\Foundation\Http\FormRequest;

final class RejectPlanChangeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('companies.view') === true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
