<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransaction extends Model
{
  use HasFactory;

  protected $fillable = [
    'recurring_template_id',
    'wallet_id',
    'category_id',
    'amount',
    'due_date',
    'status',
    'installment_number',
    'total_installments',
    'paid_at',
    'is_auto_debit',
  ];

  public function template()
  {
    return $this->belongsTo(RecurringTemplate::class, 'recurring_template_id');
  }

  public function wallet()
  {
    return $this->belongsTo(Wallet::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
