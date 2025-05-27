<?php

namespace App\Services;

use App\Models\CreditCardTransaction;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ScheduledTransaction;

class DashboardService
{
  public function getDashboardData(User $user): array
  {
    $wallet = $user->wallet;

    return [
      'walletBalance'    => $this->format($wallet->balance),
      'totalIncomes'     => $this->format($wallet->incomes->sum('amount')),
      'totalExpenses'    => $this->format($wallet->expenses->sum('amount')),
      'totalDeposits'    => $this->format($wallet->deposits->sum('amount')),
      'totalWithdrawals' => $this->format($wallet->withdrawals->sum('amount')),
      'totalCreditcard' => $this->format(CreditCardTransaction::sum('amount')),
    ];
  }

  public function getMonthlyData(User $user, int $month): array
  {
    $wallet = $user->wallet;

    $incomes     = $wallet->incomes()->whereMonth('expense_date', $month)->sum('amount');
    $expenses    = $wallet->expenses()->whereMonth('expense_date', $month)->sum('amount');
    $deposits    = $wallet->deposits()->whereMonth('expense_date', $month)->sum('amount');
    $withdrawals = $wallet->withdrawals()->whereMonth('expense_date', $month)->sum('amount');

    $balance = $incomes + $deposits - $expenses - $withdrawals;

    $installments = \App\Models\ScheduledTransaction::where('wallet_id', $wallet->id)
      ->where('status', 'pendente')
      ->whereMonth('due_date', $month)
      ->sum('amount');

    $creditCardFatura = \App\Models\CreditCardTransaction::whereHas('creditCard', function ($q) use ($user) {
        $q->where('user_id', $user->id);
      })
      ->where('is_paid', false)
      ->whereMonth('purchase_date', $month)
      ->sum('amount');

    return [
      'totalIncomes'      => $this->format($incomes),
      'totalExpenses'     => $this->format($expenses),
      'totalDeposits'     => $this->format($deposits),
      'totalWithdrawals'  => $this->format($withdrawals),
      'walletBalance'     => $this->format($balance),
      'transactions'      => $this->getTransactions($user, true, $month),
      'totalInstallments' => $this->format($installments),
      'totalCreditCards'  => $this->format($creditCardFatura),
    ];
  }

  public function getTransactions(User $user, bool $onlyCurrentMonth = false, ?int $month = null)
  {
    $wallet = $user->wallet;
    $month  = $month ?: now()->month;

    $incomes     = $wallet->incomes()->with('category');
    $deposits    = $wallet->deposits()->with('category');
    $expenses    = $wallet->expenses()->with('category');
    $withdrawals = $wallet->withdrawals()->with('category');

    if ($onlyCurrentMonth) {
      $incomes->whereMonth('expense_date', $month);
      $deposits->whereMonth('expense_date', $month);
      $expenses->whereMonth('expense_date', $month);
      $withdrawals->whereMonth('expense_date', $month);
    }

    return collect()
      ->merge($incomes->get()->map(fn($item) => $this->formatTransaction($item, 'Entrada')))
      ->merge($deposits->get()->map(fn($item) => $this->formatTransaction($item, 'Aporte')))
      ->merge($expenses->get()->map(fn($item) => $this->formatTransaction($item, 'Despesa')))
      ->merge($withdrawals->get()->map(fn($item) => $this->formatTransaction($item, 'Saída')))
      ->sortBy('date')
      ->values();
  }

  public function formatTransaction($item, string $type): array
  {
    $date = Carbon::parse($item->expense_date);

    return [
      'type'         => $type,
      'date'         => $date->format('Y-m-d'),
      'dateDisplay' => $date->format('d/m/Y'),
      'amount'       => number_format($item->amount, 2, ',', '.'),
      'description'  => $item->description,
      'category' => optional($item->category)->name,
    ];
  }

  public function format($value): string
  {
    if (is_null($value)) return '0,00';
    return number_format($value, 2, ',', '.');
  }

  public function getBalanceHistory(User $user, int $months = 6): array
  {
    $wallet = $user->wallet;
    $now = now();

    $history = [];

    for ($i = $months - 1; $i >= 0; $i--) {
      $date = $now->copy()->subMonths($i);
      $month = $date->month;
      $year = $date->year;

      $incomes     = $wallet->incomes()->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
      $deposits    = $wallet->deposits()->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
      $expenses    = $wallet->expenses()->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');
      $withdrawals = $wallet->withdrawals()->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount');

      $balance = $incomes + $deposits - $expenses - $withdrawals;

      $history[] = [
        'label' => ucfirst($date->translatedFormat('F/Y')),
        'value' => round($balance, 2),
      ];
    }
    return $history;
  }

  public function getExpensesGroupedByCategory(User $user, int $month): array
  {
    return $user->wallet
      ->expenses()
      ->with('category')
      ->whereMonth('expense_date', $month)
      ->get()
      ->groupBy(fn($expense) => optional($expense->category)->name ?? 'Sem categoria')
      ->map(fn($group) => $group->sum('amount'))
      ->map(fn($value) => round($value, 2))
      ->toArray();
  }

  public function getIncomesGroupedByCategory(User $user, int $month): array
  {
    return $user->wallet
      ->incomes()
      ->with('category')
      ->whereMonth('expense_date', $month)
      ->get()
      ->groupBy(fn($income) => optional($income->category)->name ?? 'Sem categoria')
      ->map(fn($group) => $group->sum('amount'))
      ->map(fn($value) => round($value, 2))
      ->toArray();
  }

  public function getAportesVsSaidas(User $user, int $month): array
  {
    $wallet = $user->wallet;
    $aportes = $wallet->deposits()
      ->whereMonth('expense_date', $month)
      ->sum('amount');

    $saidas = $wallet->withdrawals()
      ->whereMonth('expense_date', $month)
      ->sum('amount');

    return [
      'Aportes' => round($aportes, 2),
      'Saídas' => round($saidas, 2),
    ];
  }

  public function getDailyMovements(User $user, int $month): array
	{
		$wallet = $user->wallet;
		$movements = collect();
		$movements = $movements
		->merge($wallet->incomes()->whereMonth('expense_date', $month)->get()->map(fn($i) => ['date' => $i->expense_date, 'amount' => $i->amount]))
		->merge($wallet->deposits()->whereMonth('expense_date', $month)->get()->map(fn($d) => ['date' => $d->expense_date, 'amount' => $d->amount]))
		->merge($wallet->expenses()->whereMonth('expense_date', $month)->get()->map(fn($e) => ['date' => $e->expense_date, 'amount' => -$e->amount]))
		->merge($wallet->withdrawals()->whereMonth('expense_date', $month)->get()->map(fn($w) => ['date' => $w->expense_date, 'amount' => -$w->amount]));

		$grouped = $movements->groupBy(fn($item) => \Carbon\Carbon::parse($item['date'])->format('d'));

		return $grouped->map(fn($items) => round($items->sum('amount'), 2))->sortKeys()->toArray();
	}

	public function getTotalParcelado(User $user): string
	{
		$walletId = $user->wallet->id;

		$total = ScheduledTransaction::where('wallet_id', $walletId)
			->where('status', 'pendente')
			->sum('amount');

		return $this->format($total);
	}
}