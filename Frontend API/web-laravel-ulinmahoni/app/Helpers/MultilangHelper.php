<?php

namespace App\Helpers;

/**
 * Helper to extract locale-specific content from multilang XML strings.
 * Format: <ID>content</ID><EN>content</EN><ZH>content</ZH>
 */
class MultilangHelper
{
    /**
     * Extract content for a specific locale from a multilang XML string.
     * Falls back to ID, then returns raw string if no tags found.
     */
    public static function extract(?string $multilangString, ?string $locale = null): string
    {
        if (empty($multilangString)) return '';

        $locale = strtoupper($locale ?? app()->getLocale());

        // Try requested locale
        if (preg_match("/<{$locale}>([\s\S]*?)<\/{$locale}>/i", $multilangString, $match)) {
            return trim($match[1]);
        }

        // Fallback to ID
        if ($locale !== 'ID' && preg_match('/<ID>([\s\S]*?)<\/ID>/i', $multilangString, $match)) {
            return trim($match[1]);
        }

        // No XML tags found — return raw string
        return $multilangString;
    }
}
