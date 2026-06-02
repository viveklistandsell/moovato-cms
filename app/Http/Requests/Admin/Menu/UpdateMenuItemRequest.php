<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Menu;

use App\Models\Language;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('menus.update') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $langs = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        return [
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'link_type' => ['required', Rule::in(MenuItem::linkTypes())],
            'link_id' => ['nullable', 'integer', 'required_if:link_type,page,category'],
            'open_in_new_tab' => ['boolean'],
            'css_class' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
            'translations' => ['required', 'array'],
            'translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'translations.*.label' => ['required', 'string', 'max:255'],
            'translations.*.link_url' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf($this->input('link_type') === MenuItem::TYPE_URL),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = $this->input('link_type');
            $id = $this->input('link_id');
            if (! is_int($id) && ! ctype_digit((string) $id)) {
                return;
            }
            $id = (int) $id;

            if ($type === MenuItem::TYPE_PAGE
                && ! Page::query()->whereKey($id)->exists()) {
                $validator->errors()->add('link_id', 'Selected page does not exist.');
            }

            if ($type === MenuItem::TYPE_CATEGORY
                && ! PageCategory::query()->whereKey($id)->exists()) {
                $validator->errors()->add('link_id', 'Selected category does not exist.');
            }
        });
    }
}
