<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $table = 't_chat_attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'attachment_type',
        'thumbnail_path',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['file_url', 'thumbnail_url', 'formatted_size'];

    // RELATIONSHIPS

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id', 'id');
    }

    // SCOPES

    public function scopeImages($query)
    {
        return $query->whereIn('file_type', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('attachment_type', $type);
    }

    // ACCESSORS

    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
            return $baseUrl . '/storage/' . ltrim($this->file_path, '/');
        }
        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
            return $baseUrl . '/storage/' . ltrim($this->thumbnail_path, '/');
        }
        return $this->file_url; // Fallback to original
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // HELPER METHODS

    public function isImage()
    {
        return in_array($this->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
    }

    public function deleteFiles()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }

        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            Storage::disk('public')->delete($this->thumbnail_path);
        }
    }

    // EVENTS
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            $attachment->deleteFiles();
        });
    }
}
