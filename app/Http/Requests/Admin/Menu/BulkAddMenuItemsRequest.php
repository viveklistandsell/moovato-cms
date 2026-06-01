<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Menu;

use App\Models\Language;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Bulk-add endpoint payload — used by the "Add to Menu" buttons in the
 * left sidebar of the editor. Each row creates one menu item; translations
 * are populated by the frontend from the picked entity's translation
 * titles (or a default for Home / Custom URL).
 */
final class BulkAddMenuItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('menus.create') === true;
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
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.link_type' => ['required', Rule::in(MenuItem::linkTypes())],
            'items.*.link_id' => ['nullable', 'integer'],
            'items.*.translations' => ['required', 'array'],
            'items.*.translations.*.lang' => ['required', 'string', Rule::in($langs)],
            'items.*.translations.*.label' => ['required', 'string', 'max:255'],
            'items.*.translations.*.link_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}
