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

    public function isDownloadable(): bool
    {
        return $this->status === 'completed'
            && $this->file_path
            && Storage::disk('local')->exists($this->file_path)
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
