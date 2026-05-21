<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Page\Widget;

use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Standalone validator for the /admin/pages/{page}/widgets sync endpoint.
 *
 * The top-level rules() only checks the envelope (widgets array + each row's
 * envelope). Each widget's per-type settings/data rules are evaluated inside
 * withValidator(), so a Hero's required `alignment` is not (incorrectly)
 * applied to a Banner sitting next to it on the same page.
 */
final class SyncPageWidgetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $types = $this->container->make(WidgetRegistry::class)->types();

        return [
            'widgets' => ['present', 'array'],
            'widgets.*.id' => ['nullable', 'integer'],
            'widgets.*.type' => ['required', 'string', Rule::in($types)],
            'widgets.*.position' => ['nullable', 'integer', 'min:0'],
            'widgets.*.is_active' => ['boolean'],
            'widgets.*.settings' => ['nullable', 'array'],
            'widgets.*.translations' => ['nullable', 'array'],
            'widgets.*.translations.*' => ['array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $registry = $this->container->make(WidgetRegistry::class);

            /** @var array<int, array<string, mixed>> $widgets */
            $widgets = (array) $this->input('widgets', []);

            foreach ($widgets as $index => $row) {
                $type = is_array($row) ? ($row['type'] ?? null) : null;
                if (! is_string($type) || ! $registry->has($type)) {
                    continue;
                }

                $class = $registry->resolve($type);

                $settings = is_array($row['settings'] ?? null) ? $row['settings'] : [];
                $settingsErrors = ValidatorFacade::make($settings, $class::settingsRules())->errors();
                foreach ($settingsErrors->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $v->errors()->add("widgets.{$index}.settings.{$field}", $message);
                    }
                }

                $translations = is_array($row['translations'] ?? null) ? $row['translations'] : [];
                foreach ($translations as $lang => $data) {
                    if (! is_string($lang) || ! is_array($data)) {
                        continue;
                    }

                    $dataErrors = ValidatorFacade::make($data, $class::dataRules())->errors();
                    foreach ($dataErrors->messages() as $field => $messages) {
                        foreach ($messages as $message) {
                            $v->errors()->add("widgets.{$index}.translations.{$lang}.{$field}", $message);
                        }
                    }
                }
            }
        });
    }
}
