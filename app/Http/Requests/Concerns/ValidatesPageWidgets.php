<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;

/**
 * Shared widget-stack validation for any page form (create/update).
 *
 * Top-level rules() callers should `+= ValidatesPageWidgets::widgetEnvelopeRules()`
 * and dispatch to `validateWidgetStack($validator)` from withValidator().
 *
 * @method mixed input(string $key, mixed $default = null)
 */
trait ValidatesPageWidgets
{
    /**
     * Envelope rules that match the array shape posted by WidgetsCanvas.vue.
     *
     * @return array<string, mixed>
     */
    public function widgetEnvelopeRules(): array
    {
        $types = app(WidgetRegistry::class)->types();

        return [
            'widgets' => ['nullable', 'array'],
            'widgets.*.id' => ['nullable', 'integer'],
            'widgets.*.type' => ['required_with:widgets.*', 'string', Rule::in($types)],
            'widgets.*.position' => ['nullable', 'integer', 'min:0'],
            'widgets.*.is_active' => ['nullable', 'boolean'],
            'widgets.*.settings' => ['nullable', 'array'],
            'widgets.*.visibility' => ['nullable', 'array'],
            'widgets.*.visibility.desktop' => ['nullable', 'boolean'],
            'widgets.*.visibility.tablet' => ['nullable', 'boolean'],
            'widgets.*.visibility.mobile' => ['nullable', 'boolean'],
            'widgets.*.css_class' => ['nullable', 'string', 'max:64'],
            'widgets.*.translations' => ['nullable', 'array'],
            'widgets.*.translations.*' => ['array'],
        ];
    }

    /**
     * Run per-widget settings/data rules against the actual incoming type, so
     * a Hero's `required` doesn't accidentally fire on a Banner sitting next
     * to it. Called from withValidator() in the host request.
     */
    public function validateWidgetStack(Validator $validator): void
    {
        $registry = app(WidgetRegistry::class);

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
                    $validator->errors()->add("widgets.{$index}.settings.{$field}", $message);
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
                        $validator->errors()->add("widgets.{$index}.translations.{$lang}.{$field}", $message);
                    }
                }
            }
        }
    }
}
