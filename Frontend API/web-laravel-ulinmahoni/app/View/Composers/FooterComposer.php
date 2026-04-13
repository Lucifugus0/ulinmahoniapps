<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Injects footer CMS data into the footer blade component.
 * Queries m_footer_content, m_footer_links, m_footer_contacts,
 * m_footer_socials, and m_footer_payments tables.
 */
class FooterComposer
{
    public function compose(View $view)
    {
        try {
            /** Fetch all active links then split by link_group column */
            $allLinks = DB::table('m_footer_links')->where('status', 1)->orderBy('sort_order')->get();

            $view->with([
                'footerContent' => DB::table('m_footer_content')->where('status', 1)->get()->keyBy('key'),
                'footerQuickLinks' => $allLinks->where('link_group', 'quick_links')->values(),
                'footerBusinessLinks' => $allLinks->where('link_group', 'business')->values(),
                'footerContacts' => DB::table('m_footer_contacts')->where('status', 1)->orderBy('sort_order')->get(),
                'footerSocials' => DB::table('m_footer_socials')->where('status', 1)->orderBy('sort_order')->get(),
                'footerPayments' => DB::table('m_footer_payments')->where('status', 1)->orderBy('sort_order')->get(),
            ]);
        } catch (\Exception $e) {
            // If tables don't exist yet (pre-migration), inject empty defaults
            Log::debug('FooterComposer: tables not available — ' . $e->getMessage());
            $view->with([
                'footerContent' => collect(),
                'footerQuickLinks' => collect(),
                'footerBusinessLinks' => collect(),
                'footerContacts' => collect(),
                'footerSocials' => collect(),
                'footerPayments' => collect(),
            ]);
        }
    }
}
