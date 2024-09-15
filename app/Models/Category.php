<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function holiday_packages(): HasMany
    {
        return $this->hasMany(HolidayPackage::class);
    }

    protected static function booted()
    {
        static::creating(function ($category) {
            // Generate slug from name when creating
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            // Update slug from name when updating
            $category->slug = Str::slug($category->name);
        });
    }
}
