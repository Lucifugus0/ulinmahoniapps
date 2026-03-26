<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketAttachment model — image/file attachment on a ticket message.
 * HEIC images are converted to JPEG on upload.
 * Provides file_url and thumbnail_url accessors for display.
 */
class TicketAttachment extends Model
{
    protected $table = 't_ticket_attachments';

    protected $fillable = [
        'message_id', 'file_name', 'file_path', 'file_type',
        'file_size', 'thumbnail_path', 'created_by', 'updated_by',
    ];

    protected $appends = ['file_url', 'thumbnail_url'];

    /** The message this attachment belongs to */
    public function message()
    {
        return $this->belongsTo(TicketMessage::class, 'message_id');
    }

    /** Full URL to the file — strips storage prefixes for consistent path */
    public function getFileUrlAttribute(): string
    {
        $path = $this->normalizePath($this->file_path);
        return '/storage/' . $path;
    }

    /** Full URL to the thumbnail — falls back to file_url if no thumbnail */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return $this->file_url;
        }
        $path = $this->normalizePath($this->thumbnail_path);
        return '/storage/' . $path;
    }

    /** Strip storage path prefixes for consistent URL generation */
    private function normalizePath(string $path): string
    {
        /* Normalize backslashes to forward slashes and remove known storage prefixes */
        $path = str_replace('\\', '/', $path);
        $prefixes = ['storage/app/public/', 'public/'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }
        return $path;
    }

    /** Check if this attachment is an image by MIME type */
    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    /** Get human-readable file size */
    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
