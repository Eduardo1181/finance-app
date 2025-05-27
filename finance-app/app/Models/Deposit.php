<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'expense_date',
        'amount',
        'description',
        'category_id',
        'monthly_rate',
        'duration_months',
        'is_locked',
        'start_date',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($deposit) {
            $deposit->wallet->increment('balance', $deposit->amount);
        });

        static::deleted(function ($deposit) {
            $deposit->wallet->decrement('balance', $deposit->amount);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}