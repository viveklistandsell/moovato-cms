<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import\Support;

/**
 * Repairs "double-encoded" UTF-8 strings that come out of Excel when a
 * CSV was saved on a Windows machine (Windows-1252 → UTF-8 twice).
 *
 * Example: the German word "Maßgeschneiderte" reaches the importer as
 * "MaÃgeschneiderte" because a single UTF-8 byte sequence for `ß`
 * (0xC3 0x9F) was decoded once as Windows-1252 ("Ã" + control) and
 * re-encoded as UTF-8. Reversing that once repairs it.
 *
 * We only touch strings that actually contain the tell-tale
 * `Ã` / `Â` / `â` markers. Clean UTF-8 passes through untouched, so
 * running the fixer on already-clean files is safe.
 */
final class MojibakeFixer
{
    /**
     * Bytes that only ever appear at the START of a Windows-1252
     * misdecoded UTF-8 pair. If any of these show up in a supposedly
     * UTF-8 string, it's mojibake.
     */
    private const TELLTALES = ["\xC3", "\xC2", "\xE2\x80"];

    public static function fix(string $s): string
    {
        if ($s === '') {
            return $s;
        }
        if (! self::looksLikeMojibake($s)) {
            return $s;
        }

        $repaired = mb_convert_encoding($s, 'UTF-8', 'Windows-1252');

        return self::looksLikeMojibake($repaired) ? $s : $repaired;
    }

    private static function looksLikeMojibake(string $s): bool
    {
        foreach (self::TELLTALES as $marker) {
            if (str_contains($s, $marker)) {
                return true;
            }
        }

        return false;
    }
}
