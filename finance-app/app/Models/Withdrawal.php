<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'expense_date', 
        'amount', 
        'description',
        'category_id',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($withdrawal) {
            $withdrawal->wallet->decrement('balance', $withdrawal->amount);
        });

        static::deleted(function ($withdrawal) {
            $withdrawal->wallet->increment('balance', $withdrawal->amount);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}