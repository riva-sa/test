<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasTranslations;

    /**
     * @var array<int, string>
     */
    protected $translatable = ['name'];

    protected $fillable = [
        'name',
        'name_en',
        'logo',
        'description',
        'email',
        'phone',
        'website',
        'address',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
