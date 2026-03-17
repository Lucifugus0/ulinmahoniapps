<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatAttachment extends Model
{
    protected $table = 't_chat_attachments';

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'attachment_type',
        'thumbnail_path',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['file_url', 'thumbnail_url'];

    // Relationships
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            $path = $this->normalizePath($this->file_path);
            return '/storage/' . $path;
        }
        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            $path = $this->normalizePath($this->thumbnail_path);
            return '/storage/' . $path;
        }
        return $this->file_url;
    }

    /**
     * Normalize file path by stripping common prefixes
     * Handles paths from both web uploads and mobile uploads
     */
    protected function normalizePath(string $path): string
    {
        // Remove prefixes: "storage/app/public/", "public/", "storage/"
        $path = preg_replace('#^(storage/app/public/|public/|storage/)#', '', $path);
        return $path;
    }

    // Helper Methods
    public function isImage()
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function getFormattedSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
