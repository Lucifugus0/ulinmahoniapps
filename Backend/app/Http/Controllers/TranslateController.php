<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

/**
 * <!-- Auto-translation endpoint for admin description fields -->
 * Uses stichoza/google-translate-php (free, no API key) to translate text
 * between Indonesian, English, and Simplified Chinese.
 */
class TranslateController extends Controller
{
    /**
     * <!-- Translate text from source language to target language -->
     * POST /api/translate
     * Params: text (string), source (id|en|zh), target (id|en|zh)
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'source' => 'required|in:id,en,zh',
            'target' => 'required|in:id,en,zh',
        ]);

        try {
            // <!-- Map locale codes to Google Translate language codes -->
            $langMap = [
                'id' => 'id',
                'en' => 'en',
                'zh' => 'zh-CN',
            ];

            $source = $langMap[$request->source];
            $target = $langMap[$request->target];

            $translated = GoogleTranslate::trans($request->text, $target, $source);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'translated' => $translated,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Translation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
