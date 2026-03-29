<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * ContentController — public API endpoints for dynamic taglines and hero videos.
 * Used by both the Frontend web portal and Mobile app home pages.
 */
class ContentController extends Controller
{
    /**
     * GET /api/v1/content/tagline
     * Returns one random active tagline from the m_taglines table.
     * Falls back to null if no active taglines exist.
     */
    public function randomTagline()
    {
        $tagline = DB::table('m_taglines')
            ->where('status', 1)
            ->inRandomOrder()
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tagline' => $tagline ? $tagline->tagline : null,
            ],
        ]);
    }

    /**
     * GET /api/v1/content/hero-video
     * Returns the active hero video URL from the m_hero_videos table.
     * Video files are stored on the Backend (admin) server and served via storage symlink.
     */
    public function activeHeroVideo()
    {
        $video = DB::table('m_hero_videos')
            ->where('status', 1)
            ->first();

        /** Build URL using ADMIN_URL since videos are stored on the Backend server */
        $adminUrl = rtrim(config('app.admin_url', env('ADMIN_URL', '')), '/');
        $videoUrl = $video && $adminUrl ? $adminUrl . '/storage/' . $video->file_path : null;

        return response()->json([
            'status' => 'success',
            'data' => [
                'video_url' => $videoUrl,
                'title' => $video ? $video->title : null,
            ],
        ]);
    }
}
