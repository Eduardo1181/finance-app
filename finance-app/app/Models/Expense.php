<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
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
        static::created(function ($expense) {
            $expense->wallet->decrement('balance', $expense->amount);
        });

        static::deleted(function ($expense) {
            $expense->wallet->increment('balance', $expense->amount);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
