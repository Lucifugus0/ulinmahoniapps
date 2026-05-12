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
     * GET /api/v1/content/tagline-desc
     * Returns one random active tagline description from the m_tagline_desc table.
     * Paired with the tagline endpoint above for the home page hero section.
     * Falls back to null if no active tagline descriptions exist.
     */
    public function randomTaglineDesc()
    {
        $desc = DB::table('m_tagline_desc')
            ->where('status', 1)
            ->inRandomOrder()
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'description' => $desc ? $desc->description : null,
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

    /**
     * GET /api/v1/content/footer
     * Returns all footer CMS data (content, links, contacts, socials, payments)
     * for consumption by the web portal and mobile app.
     */
    public function footer()
    {
        $content = DB::table('m_footer_content')->where('status', 1)->get()->keyBy('key');
        $links = DB::table('m_footer_links')->where('status', 1)->orderBy('sort_order')->get();
        $contacts = DB::table('m_footer_contacts')->where('status', 1)->orderBy('sort_order')->get();
        $socials = DB::table('m_footer_socials')->where('status', 1)->orderBy('sort_order')->get();
        $payments = DB::table('m_footer_payments')->where('status', 1)->orderBy('sort_order')->get();

        /** Build icon URLs for payments — images are stored on the Backend server */
        $adminUrl = config('app.admin_url', env('ADMIN_URL', ''));
        $payments = $payments->map(function ($p) use ($adminUrl) {
            if ($p->icon_image && !str_starts_with($p->icon_image, 'http')) {
                $p->icon_url = $adminUrl . '/storage/' . $p->icon_image;
            } else {
                $p->icon_url = $p->icon_image;
            }
            return $p;
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'company_description' => $content->get('company_description')?->value,
                'copyright' => $content->get('copyright')?->value,
                'app_store_url' => $content->get('app_store_url')?->value,
                'play_store_url' => $content->get('play_store_url')?->value,
                /** Split links by link_group for quick_links vs business sections */
                'links' => $links,
                'quick_links' => $links->where('link_group', 'quick_links')->values(),
                'business_links' => $links->where('link_group', 'business')->values(),
                'contacts' => $contacts,
                'socials' => $socials,
                'payments' => $payments,
            ],
        ]);
    }

    /**
     * GET /api/v1/content/legal/{slug}
     * Returns a legal page (Terms, Privacy, Rental Agreement) by its URL slug.
     * Content is stored as multilang XML in the m_legal_pages table.
     */
    public function legalPage($slug)
    {
        $page = DB::table('m_legal_pages')->where('slug', $slug)->where('status', 1)->first();
        return response()->json([
            'status' => 'success',
            'data' => $page ? ['title' => $page->title, 'content' => $page->content, 'slug' => $page->slug] : null,
        ]);
    }
}
