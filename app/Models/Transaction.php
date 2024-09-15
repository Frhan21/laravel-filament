<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_amount',
        'user_id',
        'holiday_package_id',
        'transactionDate',
        'payment_method',
        'status'
    ];
    protected static function booted()
    {
        static::created(function ($transaction) {
            // Buat entri Payment berdasarkan data transaksi
            $transaction->payment()->create([
                'user_id' => $transaction->user_id,
                'payment_method' => $transaction->payment_method, // Pastikan field ini ada di model Payment
                'payment_status' => $transaction->status, // Status default
                'payment_deadline' => now()->addDay(), // Set deadline satu hari setelah transaksi dibuat
                'amount'=> $transaction->total_amount
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function holiday_package(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
