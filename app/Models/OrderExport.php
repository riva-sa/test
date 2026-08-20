<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderExport extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'progress',
        'total_rows',
        'processed_rows',
        'file_path',
        'file_name',
        'filters',
        'error_message',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function getRealPath(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        // 1. Storage local disk path
        $localPath = Storage::disk('local')->path($this->file_path);
        if (file_exists($localPath)) {
            return $localPath;
        }

        // 2. Direct storage_path fallback
        $storageAppPath = storage_path('app/' . ltrim($this->file_path, '/'));
        if (file_exists($storageAppPath)) {
            return $storageAppPath;
        }

        // 3. Storage public disk path
        $publicPath = Storage::disk('public')->path($this->file_path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        // 4. Absolute path
        if (file_exists($this->file_path)) {
            return $this->file_path;
        }

        return null;
    }

    public function isDownloadable(): bool
    {
        return $this->status === 'completed'
            && $this->getRealPath() !== null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function markAsProcessing(int $totalRows): void
    {
        $this->update([
            'status' => 'processing',
            'total_rows' => $totalRows,
            'progress' => 0,
            'processed_rows' => 0,
        ]);
    }

    public function updateProgress(int $processedRows): void
    {
        $progress = $this->total_rows > 0
            ? min(100, (int) round(($processedRows / $this->total_rows) * 100))
            : 0;

        $this->update([
            'processed_rows' => $processedRows,
            'progress' => $progress,
        ]);
    }

    public function markAsCompleted(string $filePath): void
    {
        $this->update([
            'status' => 'completed',
            'progress' => 100,
            'processed_rows' => $this->total_rows,
            'file_path' => $filePath,
            'completed_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
