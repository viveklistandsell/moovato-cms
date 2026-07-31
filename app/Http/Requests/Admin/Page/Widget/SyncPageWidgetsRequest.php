<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page\Widget;

use App\Http\Requests\Concerns\ValidatesPageWidgets;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Standalone validator for the /admin/pages/{page}/widgets sync endpoint.
 * Delegates all rule logic to the ValidatesPageWidgets trait so this endpoint
 * and the main page form (Store/UpdatePageRequest) validate the widget stack
 * identically — including the visibility + css_class fields.
 */
final class SyncPageWidgetsRequest extends FormRequest
{
    use ValidatesPageWidgets;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'widgets' => ['present', 'array'],
        ] + $this->widgetEnvelopeRules();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after([$this, 'validateWidgetStack']);
    }
}
