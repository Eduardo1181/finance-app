<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCardTransaction extends Model
{
    protected $fillable = [
        'credit_card_id',
        'category_id',
        'amount',
        'purchase_date',
        'description',
        'is_installment',
        'installment_number',
        'total_installments',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'is_installment' => 'boolean',
        'is_paid' => 'boolean',
        'purchase_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
