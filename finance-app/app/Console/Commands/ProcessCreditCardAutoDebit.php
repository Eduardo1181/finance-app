<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CreditCard;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessCreditCardAutoDebit extends Command
{
    protected $signature = 'credit-cards:auto-debit';
    protected $description = 'Processa o pagamento automático das faturas dos cartões de crédito';

    public function handle()
    {
        $today = now();
        $month = $today->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $cards = CreditCard::where('auto_debit', true)
            ->where('payment_day', $today->day)
            ->get();

        foreach ($cards as $card) {
            $transactions = $card->transactions()
                ->whereBetween('purchase_date', [$start, $end])
                ->where('is_paid', false)
                ->get();

            $total = $transactions->sum('amount');

            if ($total == 0) {
                $this->info("Nenhuma transação pendente para o cartão {$card->name}.");
                continue;
            }

            DB::transaction(function () use ($card, $transactions, $total, $start) {
                foreach ($transactions as $txn) {
                    $txn->update([
                        'is_paid' => true,
                        'paid_at' => now(),
                    ]);
                }

                Expense::create([
                    'wallet_id'    => $card->user->wallet->id,
                    'category_id'  => null,
                    'amount'       => $total,
                    'expense_date' => now(),
                    'description'  => 'Fatura automática: ' . $card->name . ' (' . $start->format('m/Y') . ')',
                ]);
            });

            $this->info("Fatura do cartão {$card->name} paga automaticamente. Total: R$ " . number_format($total, 2, ',', '.'));
        }

        return Command::SUCCESS;
    }
}
