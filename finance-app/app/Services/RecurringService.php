<?php 

namespace App\Services;

use App\Models\RecurringTemplate;
use App\Models\ScheduledTransaction;
use Carbon\Carbon;

class RecurringService
{
  public function createWithSchedule(array $data): RecurringTemplate
  {
    $template = RecurringTemplate::create($data);

    $this->generateInstallments($template);

    return $template;
  }

  protected function generateInstallments(RecurringTemplate $template): void
  {
    $start = Carbon::parse($template->start_date)->startOfMonth();
    $walletId = $template->wallet_id;
    $categoryId = $template->category_id;

    for ($i = 0; $i < $template->installments; $i++) {
      $dueDate = $start->copy()->addMonths($i)->day($template->due_day);

      ScheduledTransaction::create([
        'recurring_template_id' => $template->id,
        'wallet_id'             => $walletId,
        'category_id'           => $categoryId,
        'amount'                => $template->amount,
        'due_date'              => $dueDate,
        'status'                => 'pendente',
        'installment_number'    => $i + 1,
        'total_installments'    => $template->installments,
        'is_auto_debit'         => $template->auto_debit,
      ]);
    }
  }
}