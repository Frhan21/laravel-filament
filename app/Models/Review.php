<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'holiday_package_id',
        'rating',
        'comment'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function holiday_package(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
