<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\CreditCardTransaction;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreditCardController extends Controller
{
    public function index()
    {
        $cards = CreditCard::where('user_id', Auth::id())->get();
        return view('credit_cards.index', compact('cards'));
    }

    public function create()
    {
        return view('credit_cards.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'limit_amount' => 'required|numeric|min:0',
            'closing_day' => 'required|integer|min:1|max:31',
            'payment_day' => 'required|integer|min:1|max:31',
            'auto_debit' => 'boolean',
            'type' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        $data['auto_debit'] = $request->has('auto_debit');

        CreditCard::create($data);

        return redirect()->route('credit_cards.index')->with('success', 'Cartão adicionado com sucesso.');
    }

    public function show(CreditCard $creditCard, Request $request)
    {
        $this->authorize('view', $creditCard);
        $monthInput = $request->query('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $months = collect(range(1, 12))->map(function ($m) use ($monthInput) {
            $monthFormatted = now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            return [
                'value' => $monthFormatted,
                'label' => ucfirst(Carbon::create()->month($m)->translatedFormat('F')),
                'selected' => $monthFormatted == $monthInput,
            ];
        })->toArray();

        $allTransactions = $creditCard->transactions()
            ->orderBy('purchase_date')
            ->get()
            ->groupBy('description');

        $month = Carbon::createFromFormat('Y-m', $monthInput)->month;
        $year = Carbon::createFromFormat('Y-m', $monthInput)->year;

        return view('credit_cards.show', compact('creditCard', 'allTransactions', 'month', 'year', 'months'));
    }

    public function payInvoice(CreditCard $creditCard, Request $request)
    {
        $this->authorize('view', $creditCard);

        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $transactions = $creditCard->transactions()
            ->whereBetween('purchase_date', [$start, $end])
            ->where('is_paid', false)
            ->get();

        $total = $transactions->sum('amount');

        if ($total == 0) {
            return redirect()->back()->with('info', 'Nenhuma transação pendente para pagar neste mês.');
        }

        DB::transaction(function () use ($transactions, $creditCard, $total, $start) {
            foreach ($transactions as $txn) {
                $txn->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                ]);
            }

            Expense::create([
                'wallet_id'    => auth()->user()->wallet->id,
                'category_id'  => null,
                'amount'       => $total,
                'expense_date' => now(),
                'description'  => 'Pagamento da fatura do cartão: ' . $creditCard->name . ' (' . $start->format('m/Y') . ')',
            ]);
        });

        return redirect()->route('credit_cards.show', $creditCard)->with('success', 'Fatura paga com sucesso!');
    }

    public function payInstallment(CreditCardTransaction $transaction)
    {
        $this->authorize('view', $transaction->creditCard);

        if ($transaction->is_paid) {
            return back()->with('info', 'Essa parcela já foi paga.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update([
                'is_paid' => true,
                'paid_at' => now(),
            ]);

            Expense::create([
                'wallet_id'    => auth()->user()->wallet->id,
                'category_id'  => null,
                'amount'       => $transaction->amount,
                'expense_date' => now(),
                'description'  => 'Pagamento de parcela do cartão: ' . ($transaction->description ?? 'Sem descrição'),
            ]);
        });

        return back()->with('success', 'Parcela paga com sucesso!');
    }
}