<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'department',
        'message',
        'status', // For tracking if the contact has been processed
    ];

    /**
     * Get the status label for this contact.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'جديد',
            'processing' => 'قيد المعالجة',
            'completed' => 'تم الرد',
            'archived' => 'مؤرشف',
            default => 'جديد',
        };
    }
}
