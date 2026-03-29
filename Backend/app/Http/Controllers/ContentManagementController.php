<?php

namespace App\Http\Controllers;

use App\Models\Tagline;
use App\Models\HeroVideo;
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
}
