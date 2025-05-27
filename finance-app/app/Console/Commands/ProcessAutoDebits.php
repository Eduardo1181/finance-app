<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledTransaction;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Deposit;
use App\Models\Withdrawal;

class ProcessAutoDebits extends Command
{
  protected $signature = 'debitos:processar';
  protected $description = 'Processa parcelas com débito automático na data de vencimento';

  public function handle(): int
  {
    $hoje = Carbon::today();

    $parcelas = ScheduledTransaction::with('template')
      ->whereDate('due_date', $hoje)
      ->where('status', 'pendente')
      ->where('is_auto_debit', true)
      ->get();

    $this->info("Encontradas {$parcelas->count()} parcelas para processar...");

    foreach ($parcelas as $parcela) {
      $parcela->update([
        'status' => 'pago',
        'paid_at' => now(),
      ]);

      $this->criarTransacao($parcela);
    }

    $this->info('Processamento finalizado.');
    return 0;
  }

  private function criarTransacao(ScheduledTransaction $p)
  {
    $type = $p->template->type;
    $modelMap = [
      'income'     => Income::class,
      'expense'    => Expense::class,
      'deposit'    => Deposit::class,
      'withdrawal' => Withdrawal::class,
    ];

    $model = $modelMap[$type] ?? null;
    if (!$model) return;

    $model::create([
      'wallet_id'    => $p->wallet_id,
      'category_id'  => $p->category_id,
      'amount'       => $p->amount,
      'expense_date' => $p->due_date,
      'description'  => $p->template->description . " (Auto - {$p->installment_number}/{$p->total_installments})"
    ]);
  }
}