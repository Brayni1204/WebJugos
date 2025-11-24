<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'imageable_id',
        'imageable_type',
    ];
    public function imageable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute($value)
    {
        // If it's already a full URL (from the migration or a new upload), return it.
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // If it's a public ID (from a new upload using store()), build the Cloudinary URL.
        try {
            // Check if the value looks like a Cloudinary public ID
            if (preg_match('/^[a-zA-Z0-9_\-\/]+$/', $value)) {
                return \Cloudinary::getUrl($value);
            }
        } catch (\Exception $e) {
            // If Cloudinary is not configured or fails, fall back to local storage
            return asset('storage/' . $value);
        }

        // Fallback for any other case
        return asset('storage/' . $value);
    }
}
