<?php

namespace App\Models;

use App\Models\CreditCardTransaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CreditCard extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'limit_amount',
        'closing_day',
        'payment_day',
        'auto_debit',
        'type',
    ];

    protected $casts = [
        'auto_debit' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditCardTransaction::class);
    }

    public function getAvailableLimitAttribute()
    {
        $used = $this->transactions()
            ->where('is_paid', false)
            ->sum('amount');

        return $this->limit_amount - $used;
    }
}