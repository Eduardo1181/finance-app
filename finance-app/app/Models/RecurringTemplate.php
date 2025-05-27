<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'wallet_id',
    'category_id',
    'type',
    'amount',
    'description',
    'start_date',
    'installments',
    'due_day',
    'auto_debit',
    'is_active',
  ];

  public function wallet()
  {
    return $this->belongsTo(Wallet::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function scheduledTransactions()
  {
    return $this->hasMany(ScheduledTransaction::class);
  }
}
