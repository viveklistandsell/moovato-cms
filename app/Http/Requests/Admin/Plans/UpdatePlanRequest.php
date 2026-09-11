<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Plans;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin-side plan edit form. Slug is locked (matches PlanTier enum
 * so we don't accept it here) — every other flexible field is
 * validated as strictly as the DB column expects.
 *
 * The `features` map keys are constrained to the set defined in
 * config/plans.php:features so a typo can't ship a new-looking gate
 * that nothing else in the code base ever checks. Same for caps.
 */
final class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.site') === true;
    }

    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        $featureKeys = array_values(array_filter(
            (array) config('plans.features', []),
            fn (string $k): bool => $k !== 'reply_reviews',
        ));
        $capKeys = ['contacts', 'services', 'areas', 'gallery', 'faqs', 'reply_reviews'];

        $rules = [
            'label' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0', 'max:999999'],
            'currency' => ['required', 'string', 'max:8'],
            'period' => ['required', 'string', 'max:32'],
            'positioning' => ['nullable', 'string', 'max:255'],
            'placement' => ['required', Rule::in(['standard', 'boosted', 'featured'])],
            'lead_url' => ['nullable', 'string', 'max:2048'],
            'lead_label' => ['nullable', 'string', 'max:80'],
            'is_active' => ['required', 'boolean'],
            'features' => ['required', 'array'],
            'caps' => ['required', 'array'],
        ];

        foreach ($featureKeys as $key) {
            $rules["features.$key"] = ['required', 'boolean'];
        }

        foreach ($capKeys as $key) {
            $rules["caps.$key"] = ['nullable', 'integer', 'min:0', 'max:999999'];
        }

        return $rules;
    }
}
