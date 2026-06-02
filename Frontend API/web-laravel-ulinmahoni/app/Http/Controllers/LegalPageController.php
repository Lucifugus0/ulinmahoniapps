<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Helpers\MultilangHelper;

/**
 * Renders legal pages (Terms, Privacy, Rental Agreement) from CMS database.
 * Falls back to legacy static blade templates if no DB content exists.
 */
class LegalPageController extends Controller
{
    public function show($slug)
    {
        $page = DB::table('m_legal_pages')->where('slug', $slug)->where('status', 1)->first();

        if (!$page || empty($page->content)) {
            // Fallback to legacy static templates
            $legacyViews = [
                'privacy-policy' => 'pages.privacy-policy.privacy-policy',
                'terms-of-services' => 'pages.terms-of-services.terms-of-services',
                'rental-agreement' => 'pages.rental-agreement.rental-agreement',
            ];

            if (isset($legacyViews[$slug])) {
                return view($legacyViews[$slug]);
            }

            abort(404);
        }

        return view('pages.legal.show', [
            'title' => MultilangHelper::extract($page->title),
            'content' => MultilangHelper::extract($page->content),
            'slug' => $slug,
        ]);
    }
}
