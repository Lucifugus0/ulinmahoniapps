<?php
/**
 * One-time script to translate all property and room descriptions
 * in production DB from Indonesian to English and Simplified Chinese.
 * Uses stichoza/google-translate-php directly.
 *
 * Usage: php translate_descriptions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Helpers\DescriptionHelper;

$langMap = ['en' => 'en', 'zh' => 'zh-CN'];

function translateText(string $text, string $targetLang): string
{
    try {
        // Add delay to avoid rate limiting
        usleep(500000); // 0.5 second
        return GoogleTranslate::trans($text, $targetLang, 'id');
    } catch (\Exception $e) {
        echo "  [WARN] Translation failed for {$targetLang}: {$e->getMessage()}\n";
        return '';
    }
}

// --- Translate Properties ---
echo "=== TRANSLATING PROPERTIES ===\n";
$properties = \DB::table('m_properties')
    ->whereNotNull('description')
    ->where('description', '!=', '')
    ->get(['idrec', 'description']);

foreach ($properties as $prop) {
    $parsed = DescriptionHelper::parse($prop->description);

    // Skip if already has EN tag
    if (!empty($parsed['en'])) {
        echo "Property #{$prop->idrec}: already translated, skipping\n";
        continue;
    }

    $idText = $parsed['id'];
    if (empty($idText)) {
        echo "Property #{$prop->idrec}: no ID text, skipping\n";
        continue;
    }

    echo "Property #{$prop->idrec}: translating...\n";

    $enText = translateText($idText, 'en');
    $zhText = translateText($idText, 'zh-CN');

    $composed = DescriptionHelper::compose([
        'id' => $idText,
        'en' => $enText,
        'zh' => $zhText,
    ]);

    \DB::table('m_properties')->where('idrec', $prop->idrec)->update(['description' => $composed]);
    echo "  EN: " . substr($enText, 0, 60) . "...\n";
    echo "  ZH: " . substr($zhText, 0, 60) . "...\n";
    echo "  Done.\n";
}

// --- Translate Rooms ---
echo "\n=== TRANSLATING ROOMS ===\n";
$rooms = \DB::table('m_rooms')
    ->whereNotNull('descriptions')
    ->where('descriptions', '!=', '')
    ->get(['idrec', 'descriptions']);

// Group by unique description to avoid translating duplicates
$uniqueDescs = [];
foreach ($rooms as $room) {
    $parsed = DescriptionHelper::parse($room->descriptions);
    if (!empty($parsed['en'])) continue; // already translated
    $idText = $parsed['id'];
    if (empty($idText)) continue;

    $key = md5($idText);
    if (!isset($uniqueDescs[$key])) {
        $uniqueDescs[$key] = [
            'id_text' => $idText,
            'room_ids' => [],
        ];
    }
    $uniqueDescs[$key]['room_ids'][] = $room->idrec;
}

echo "Found " . count($uniqueDescs) . " unique descriptions to translate\n\n";

$count = 0;
foreach ($uniqueDescs as $key => $data) {
    $count++;
    $roomIds = $data['room_ids'];
    $idText = $data['id_text'];

    echo "[{$count}/" . count($uniqueDescs) . "] Rooms " . implode(',', array_slice($roomIds, 0, 5));
    if (count($roomIds) > 5) echo " (+" . (count($roomIds) - 5) . " more)";
    echo ": translating...\n";

    $enText = translateText($idText, 'en');
    $zhText = translateText($idText, 'zh-CN');

    $composed = DescriptionHelper::compose([
        'id' => $idText,
        'en' => $enText,
        'zh' => $zhText,
    ]);

    \DB::table('m_rooms')->whereIn('idrec', $roomIds)->update(['descriptions' => $composed]);
    echo "  EN: " . substr($enText, 0, 60) . "...\n";
    echo "  ZH: " . substr($zhText, 0, 60) . "...\n";
    echo "  Updated " . count($roomIds) . " room(s).\n";
}

echo "\n=== DONE ===\n";
echo "Properties translated: " . $properties->count() . "\n";
echo "Unique room descriptions translated: " . count($uniqueDescs) . "\n";
echo "Total rooms updated: " . $rooms->count() . "\n";
