<?php

namespace App\Helpers;

/**
 * Multi-language description helper.
 * Parses XML-tagged descriptions: <ID>...</ID><EN>...</EN><ZH>...</ZH>
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

    /**
     * <!-- Sanitize HTML description: allow safe formatting tags only -->
     * Strips dangerous tags (script, iframe, etc.) while keeping
     * bold, italic, color, lists, links, and paragraphs from Quill editor.
     */
    public static function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Allow only safe HTML tags from the Quill rich text editor
        $allowed = '<p><br><strong><b><em><i><u><s><ol><ul><li><a><span><h1><h2><h3>';
        $clean = strip_tags($html, $allowed);

        // Remove any on* event handlers from remaining tags (e.g. onclick, onerror)
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);

        return $clean;
    }

    /**
     * <!-- Get sanitized HTML description for display -->
     * Returns safe HTML ready for {!! !!} output in Blade templates.
     * For plain text (legacy data without HTML), wraps in <p> with nl2br.
     */
    public static function getHtml(?string $raw, string $locale = 'id'): string
    {
        $text = self::get($raw, $locale);
        if (empty($text)) {
            return '';
        }

        // If content has HTML tags, sanitize and return
        if ($text !== strip_tags($text)) {
            return self::sanitize($text);
        }

        // Plain text (legacy): escape, convert newlines, wrap in paragraph
        return nl2br(e($text));
    }
}
