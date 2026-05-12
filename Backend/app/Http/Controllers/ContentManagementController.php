<?php

namespace App\Http\Controllers;

use App\Models\Tagline;
use App\Models\TaglineDesc;
use App\Models\HeroVideo;
use App\Models\FooterContent;
use App\Models\FooterLink;
use App\Models\FooterContact;
use App\Models\FooterSocial;
use App\Models\FooterPayment;
use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * ContentManagementController — handles CRUD for taglines and hero videos.
 * Taglines are displayed randomly on Frontend and Mobile home pages.
 * Hero videos are background videos on home pages; only one can be active at a time.
 */
class ContentManagementController extends Controller
{
    /** Render the content management page with tagline and video tabs */
    public function index()
    {
        return view('pages.settings.content-management');
    }

    // ==================== TAGLINE METHODS ====================

    /** GET — return all taglines as JSON */
    public function taglineList()
    {
        $taglines = Tagline::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $taglines]);
    }

    /** POST — create a new tagline */
    public function taglineStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tagline' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tagline = Tagline::create([
            'tagline' => $request->tagline,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $tagline, 'message' => 'Tagline created successfully.']);
    }

    /** PUT — update an existing tagline */
    public function taglineUpdate(Request $request, $id)
    {
        $tagline = Tagline::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tagline' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->has('tagline')) {
            $tagline->tagline = $request->tagline;
        }
        if ($request->has('status')) {
            $tagline->status = $request->status;
        }
        $tagline->updated_by = Auth::id();
        $tagline->save();

        return response()->json(['success' => true, 'data' => $tagline, 'message' => 'Tagline updated successfully.']);
    }

    /** DELETE — remove a tagline */
    public function taglineDestroy($id)
    {
        $tagline = Tagline::findOrFail($id);
        $tagline->delete();

        return response()->json(['success' => true, 'message' => 'Tagline deleted successfully.']);
    }

    // ==================== TAGLINE DESCRIPTION METHODS ====================
    // <!-- Tagline descriptions are paired with taglines on home pages; CRUD mirrors taglines exactly except the column is `description` (text) instead of `tagline` (varchar 255). -->

    /** GET — return all tagline descriptions as JSON */
    public function taglineDescList()
    {
        $descs = TaglineDesc::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $descs]);
    }

    /** POST — create a new tagline description */
    public function taglineDescStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $desc = TaglineDesc::create([
            'description' => $request->description,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $desc, 'message' => 'Tagline description created successfully.']);
    }

    /** PUT — update an existing tagline description (text and/or status) */
    public function taglineDescUpdate(Request $request, $id)
    {
        $desc = TaglineDesc::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string|max:1000',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->has('description')) {
            $desc->description = $request->description;
        }
        if ($request->has('status')) {
            $desc->status = $request->status;
        }
        $desc->updated_by = Auth::id();
        $desc->save();

        return response()->json(['success' => true, 'data' => $desc, 'message' => 'Tagline description updated successfully.']);
    }

    /** DELETE — remove a tagline description */
    public function taglineDescDestroy($id)
    {
        $desc = TaglineDesc::findOrFail($id);
        $desc->delete();

        return response()->json(['success' => true, 'message' => 'Tagline description deleted successfully.']);
    }

    // ==================== HERO VIDEO METHODS ====================

    /** GET — return all hero videos as JSON */
    public function videoList()
    {
        $videos = HeroVideo::orderBy('created_at', 'desc')->get()->map(function ($video) {
            $video->video_url = $video->video_url;
            $video->file_size_formatted = $this->formatFileSize($video->file_size);
            return $video;
        });

        return response()->json(['success' => true, 'data' => $videos]);
    }

    /** POST — upload a new hero video (MP4, max 100MB) */
    public function videoStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4|max:102400',
        ], [
            'video.max' => 'Video file must not exceed 100MB.',
            'video.mimes' => 'Only MP4 format is allowed.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $file = $request->file('video');
        $filename = 'hero_' . time() . '_' . uniqid() . '.mp4';
        $path = $file->storeAs('hero_videos', $filename, 'public');

        $video = HeroVideo::create([
            'title' => $request->title,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'status' => 0,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $video->video_url = $video->video_url;
        $video->file_size_formatted = $this->formatFileSize($video->file_size);

        return response()->json(['success' => true, 'data' => $video, 'message' => 'Video uploaded successfully.']);
    }

    /** POST — activate a video (deactivates all others in a transaction) */
    public function videoActivate($id)
    {
        DB::transaction(function () use ($id) {
            /** Deactivate all videos first */
            HeroVideo::where('status', 1)->update(['status' => 0, 'updated_by' => Auth::id()]);
            /** Activate the selected video */
            HeroVideo::where('idrec', $id)->update(['status' => 1, 'updated_by' => Auth::id()]);
        });

        return response()->json(['success' => true, 'message' => 'Video activated successfully.']);
    }

    /** DELETE — remove a video file and database record */
    public function videoDestroy($id)
    {
        $video = HeroVideo::findOrFail($id);

        /** Prevent deleting the only active video */
        if ($video->status === 1) {
            return response()->json(['success' => false, 'message' => 'Cannot delete the active video. Activate another video first.'], 422);
        }

        /** Delete the file from storage */
        if (Storage::disk('public')->exists($video->file_path)) {
            Storage::disk('public')->delete($video->file_path);
        }

        $video->delete();

        return response()->json(['success' => true, 'message' => 'Video deleted successfully.']);
    }

    /** Helper: format file size to human-readable string */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    // ==================== FOOTER CONTENT (key-value rich text) ====================

    /** GET — return all footer content rows as JSON */
    public function footerContentList()
    {
        $content = FooterContent::orderBy('key')->get();
        return response()->json(['success' => true, 'data' => $content]);
    }

    /** PUT — upsert a footer content row by key */
    public function footerContentUpdate(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $content = FooterContent::updateOrCreate(
            ['key' => $key],
            ['value' => $request->value, 'status' => 1, 'updated_by' => Auth::id()]
        );

        if ($content->wasRecentlyCreated) {
            $content->created_by = Auth::id();
            $content->save();
        }

        return response()->json(['success' => true, 'data' => $content, 'message' => 'Footer content saved.']);
    }

    // ==================== FOOTER LINKS ====================

    /** GET — return all footer links as JSON */
    public function footerLinkList()
    {
        $links = FooterLink::orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $links]);
    }

    /** POST — create a new footer link */
    public function footerLinkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'link_group' => 'nullable|string|in:quick_links,business',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $link = FooterLink::create([
            'label' => $request->label,
            'url' => $request->url,
            'link_group' => $request->link_group ?? 'quick_links',
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $link, 'message' => 'Link created.']);
    }

    /** PUT — update an existing footer link */
    public function footerLinkUpdate(Request $request, $id)
    {
        $link = FooterLink::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|string|max:500',
            'link_group' => 'sometimes|string|in:quick_links,business',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $link->fill($request->only(['label', 'url', 'link_group', 'sort_order', 'status']));
        $link->updated_by = Auth::id();
        $link->save();

        return response()->json(['success' => true, 'data' => $link, 'message' => 'Link updated.']);
    }

    /** DELETE — remove a footer link */
    public function footerLinkDestroy($id)
    {
        FooterLink::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Link deleted.']);
    }

    // ==================== FOOTER CONTACTS ====================

    /** GET — return all footer contacts as JSON */
    public function footerContactList()
    {
        $contacts = FooterContact::orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $contacts]);
    }

    /** POST — create a new footer contact */
    public function footerContactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon_class' => 'nullable|string|max:100',
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:500',
            'link_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $contact = FooterContact::create([
            'icon_class' => $request->icon_class,
            'label' => $request->label,
            'value' => $request->value,
            'link_url' => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $contact, 'message' => 'Contact created.']);
    }

    /** PUT — update an existing footer contact */
    public function footerContactUpdate(Request $request, $id)
    {
        $contact = FooterContact::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'icon_class' => 'nullable|string|max:100',
            'label' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|string|max:500',
            'link_url' => 'nullable|string|max:500',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $contact->fill($request->only(['icon_class', 'label', 'value', 'link_url', 'sort_order', 'status']));
        $contact->updated_by = Auth::id();
        $contact->save();

        return response()->json(['success' => true, 'data' => $contact, 'message' => 'Contact updated.']);
    }

    /** DELETE — remove a footer contact */
    public function footerContactDestroy($id)
    {
        FooterContact::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Contact deleted.']);
    }

    // ==================== FOOTER SOCIALS ====================

    /** GET — return all footer social links as JSON */
    public function footerSocialList()
    {
        $socials = FooterSocial::orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $socials]);
    }

    /** POST — create a new footer social link */
    public function footerSocialStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'icon_class' => 'nullable|string|max:100',
            'icon_image' => 'nullable|string|max:500',
            'url' => 'required|string|max:500',
            'hover_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $social = FooterSocial::create([
            'name' => $request->name,
            'icon_class' => $request->icon_class,
            'icon_image' => $request->icon_image,
            'url' => $request->url,
            'hover_color' => $request->hover_color,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $social, 'message' => 'Social link created.']);
    }

    /** PUT — update an existing footer social link */
    public function footerSocialUpdate(Request $request, $id)
    {
        $social = FooterSocial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'icon_class' => 'nullable|string|max:100',
            'icon_image' => 'nullable|string|max:500',
            'url' => 'sometimes|required|string|max:500',
            'hover_color' => 'nullable|string|max:50',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $social->fill($request->only(['name', 'icon_class', 'icon_image', 'url', 'hover_color', 'sort_order', 'status']));
        $social->updated_by = Auth::id();
        $social->save();

        return response()->json(['success' => true, 'data' => $social, 'message' => 'Social link updated.']);
    }

    /** DELETE — remove a footer social link */
    public function footerSocialDestroy($id)
    {
        FooterSocial::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Social link deleted.']);
    }

    // ==================== FOOTER PAYMENTS ====================

    /** GET — return all footer payment methods as JSON */
    public function footerPaymentList()
    {
        $payments = FooterPayment::orderBy('sort_order')->get()->map(function ($p) {
            /** Build full URL for uploaded icons */
            if ($p->icon_image && !str_starts_with($p->icon_image, 'http')) {
                $p->icon_url = asset('storage/' . $p->icon_image);
            } else {
                $p->icon_url = $p->icon_image;
            }
            return $p;
        });
        return response()->json(['success' => true, 'data' => $payments]);
    }

    /** POST — create a new footer payment method with optional icon upload */
    public function footerPaymentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $iconImage = $request->icon_url;
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = 'payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $iconImage = $file->storeAs('footer_payments', $filename, 'public');
        }

        $payment = FooterPayment::create([
            'name' => $request->name,
            'icon_image' => $iconImage,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'data' => $payment, 'message' => 'Payment method created.']);
    }

    /** PUT — update an existing footer payment method */
    public function footerPaymentUpdate(Request $request, $id)
    {
        $payment = FooterPayment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon_url' => 'nullable|string|max:500',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('icon')) {
            /** Delete old icon if it was an uploaded file */
            if ($payment->icon_image && !str_starts_with($payment->icon_image, 'http') && Storage::disk('public')->exists($payment->icon_image)) {
                Storage::disk('public')->delete($payment->icon_image);
            }
            $file = $request->file('icon');
            $filename = 'payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $payment->icon_image = $file->storeAs('footer_payments', $filename, 'public');
        } elseif ($request->has('icon_url')) {
            $payment->icon_image = $request->icon_url;
        }

        if ($request->has('name')) $payment->name = $request->name;
        if ($request->has('sort_order')) $payment->sort_order = $request->sort_order;
        if ($request->has('status')) $payment->status = $request->status;
        $payment->updated_by = Auth::id();
        $payment->save();

        return response()->json(['success' => true, 'data' => $payment, 'message' => 'Payment method updated.']);
    }

    /** DELETE — remove a footer payment method and its icon file */
    public function footerPaymentDestroy($id)
    {
        $payment = FooterPayment::findOrFail($id);

        /** Delete uploaded icon file */
        if ($payment->icon_image && !str_starts_with($payment->icon_image, 'http') && Storage::disk('public')->exists($payment->icon_image)) {
            Storage::disk('public')->delete($payment->icon_image);
        }

        $payment->delete();
        return response()->json(['success' => true, 'message' => 'Payment method deleted.']);
    }

    // ==================== LEGAL PAGES ====================

    /** GET — return all legal pages as JSON */
    public function legalPageList()
    {
        $pages = LegalPage::orderBy('slug')->get();
        return response()->json(['success' => true, 'data' => $pages]);
    }

    /** GET — return a single legal page by slug as JSON */
    public function legalPageShow($slug)
    {
        $page = LegalPage::where('slug', $slug)->first();
        if (!$page) {
            return response()->json(['success' => false, 'message' => 'Page not found.'], 404);
        }
        return response()->json(['success' => true, 'data' => $page]);
    }

    /** PUT — update a legal page by slug (no create/delete — fixed set of 3 pages) */
    public function legalPageUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $page = LegalPage::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $request->title,
                'content' => $request->content,
                'status' => 1,
                'updated_by' => Auth::id(),
            ]
        );

        if ($page->wasRecentlyCreated) {
            $page->created_by = Auth::id();
            $page->save();
        }

        return response()->json(['success' => true, 'data' => $page, 'message' => 'Legal page saved.']);
    }
}
