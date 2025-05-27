<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
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
        static::created(function ($income) {
            $income->wallet->increment('balance', $income->amount);
        });

        static::deleted(function ($income) {
            $income->wallet->decrement('balance', $income->amount);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
