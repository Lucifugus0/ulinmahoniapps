<?php

namespace App\Helpers;

/**
 * Multi-language description helper.
 * Parses and composes XML-tagged descriptions: <ID>...</ID><EN>...</EN><ZH>...</ZH>
 * Backward compatible — untagged text is treated as Indonesian (ID).
 */
class DescriptionHelper
{
    /**
     * <!-- Parse XML-tagged description into associative array of languages -->
     * Returns ['id' => '...', 'en' => '...', 'zh' => '...']
     * Untagged text is treated as Indonesian for backward compatibility.
     */
    public static function parse(?string $raw): array
    {
        $result = ['id' => '', 'en' => '', 'zh' => ''];

        if (empty($raw)) {
            return $result;
        }

        $hasTag = false;

        // <!-- Match XML-style language tags with DOTALL for multiline content -->
        if (preg_match_all('/<(ID|EN|ZH)>(.*?)<\/\1>/si', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $lang = strtolower($match[1]);
                $result[$lang] = trim($match[2]);
                $hasTag = true;
            }
        }

        // <!-- Backward compatibility: no tags found, treat entire text as Indonesian -->
        if (!$hasTag) {
            $result['id'] = trim($raw);
        }

        return $result;
    }

    /**
     * <!-- Compose language array back into XML-tagged string for storage -->
     * Skips empty languages to keep the stored value clean.
     */
    public static function compose(array $descriptions): string
    {
        $parts = [];

        foreach (['id', 'en', 'zh'] as $lang) {
            $text = trim($descriptions[$lang] ?? '');
            if ($text !== '') {
                $tag = strtoupper($lang);
                $parts[] = "<{$tag}>{$text}</{$tag}>";
            }
        }

        return implode("\n", $parts);
    }

    /**
     * <!-- Get description for a specific locale with fallback chain -->
     * Fallback order: requested locale -> EN -> ID -> raw string
     */
    public static function get(?string $raw, string $locale = 'id'): string
    {
        if (empty($raw)) {
            return '';
        }

        $parsed = self::parse($raw);
        $locale = strtolower($locale);

        // <!-- Fallback chain: requested locale -> EN -> ID -->
        if (!empty($parsed[$locale])) {
            return $parsed[$locale];
        }
        if (!empty($parsed['en'])) {
            return $parsed['en'];
        }
        if (!empty($parsed['id'])) {
            return $parsed['id'];
        }

        // <!-- Final fallback: return raw string (for untagged legacy data) -->
        return trim($raw);
    }
}
