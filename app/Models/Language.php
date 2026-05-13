<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'native_name', 'code', 'flag', 'status', 'is_rtl', 'lang_locale', 'lang_is_default', 'sort_order'])]
final class Language extends Model
{
    public static function default(): ?self
    {
        return self::query()->where('lang_is_default', true)->where('status', true)->first();
    }

    public static function active(): Collection
    {
        return self::query()->where('status', true)->orderBy('sort_order')->get();
    }

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_rtl' => 'boolean',
            'lang_is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
