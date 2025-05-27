<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use App\Services\RecurringService;
use App\Models\ScheduledTransaction;
use App\Models\Withdrawal;
use Carbon\Carbon;

class RecurringController extends Controller
{
    public function store(Request $request, RecurringService $service)
    {
        $data = $request->validate([
        'wallet_id'     => 'required|exists:wallets,id',
        'category_id'   => 'nullable|exists:categories,id',
        'type'          => 'required|in:income,expense,deposit,withdrawal',
        'amount'        => 'required|numeric|min:0.01',
        'description'   => 'nullable|string|max:255',
        'start_date'    => 'required|date',
        'installments'  => 'required|integer|min:1',
        'due_day'       => [
            'required',
            'integer',
            'min:1',
            function ($attribute, $value, $fail) use ($request) {
                $this->validateDueDay($value, $request->input('start_date'), $fail, $attribute);
            }          
        ],
        'auto_debit'    => 'boolean'
        ]);
        $data['auto_debit'] = isset($data['auto_debit']) && $data['auto_debit'] ? true : false;
        $template = $service->createWithSchedule($data);
        return response()->json([
        'message' => 'Lançamento parcelado criado com sucesso!',
        'template' => $template
        ]);
    }

    public function validateDueDay($dueDay, $startDate, $fail, $attribute = 'due_day'): void
    {
        if (!$startDate) return;

        $daysInMonth = \Carbon\Carbon::parse($startDate)->daysInMonth;

        if ($dueDay > $daysInMonth) {
            $fail("O campo {$attribute} não pode ser maior que $daysInMonth dias no mês de início.");
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        $month = (int) $request->input('month', now()->month);

        $totalInstallments = ScheduledTransaction::where('wallet_id', $wallet->id)
            ->where('status', 'pendente')
            ->sum('amount');
        $totalInstallments = number_format($totalInstallments, 2, ',', '.');

        $totalVencidas = ScheduledTransaction::where('wallet_id', $wallet->id)
            ->where('status', 'pendente')
            ->where('due_date', '<', now()->startOfDay())
            ->sum('amount');
        $totalVencidas = number_format($totalVencidas, 2, ',', '.');

        $todasParcelas = ScheduledTransaction::with(['template', 'category'])
            ->where('wallet_id', $wallet->id)
            ->where('status', 'pendente')
            ->orderBy('due_date')
            ->get()
            ->groupBy('recurring_template_id');

        $months = collect(range(1, 12))->map(function ($m) use ($month) {
            return [
                'value' => $m,
                'label' => ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')),
                'selected' => $m == $month,
            ];
        });

        return view('installments', compact('todasParcelas', 'months', 'totalInstallments', 'totalVencidas', 'month'));
    }

    public function confirmar($id)
    {
        $parcela = ScheduledTransaction::with('template')->findOrFail($id);

        if ($parcela->status === 'pago') {
            return back()->with('error', 'Essa parcela já foi paga.');
        }
        
        $parcela->update([
            'status' => 'pago',
            'paid_at' => Carbon::now(),
        ]);
        $this->criarTransacaoReal($parcela);

        return back()->with('success', 'Parcela confirmada e lançada com sucesso!');
    }

    private function criarTransacaoReal(ScheduledTransaction $parcela)
    {
        $type = $parcela->template->type;
        $modelMap = [
            'income'     => Income::class,
            'expense'    => Expense::class,
            'deposit'    => Deposit::class,
            'withdrawal' => Withdrawal::class,
        ];

        $model = $modelMap[$type] ?? null;
        if (!$model) return;

        $model::create([
            'wallet_id'    => $parcela->wallet_id,
            'category_id'  => $parcela->category_id,
            'amount'       => $parcela->amount,
            'expense_date' => $parcela->due_date,
            'description'  => $parcela->template->description . " (Parcela {$parcela->installment_number}/{$parcela->total_installments})",
        ]);
    }

}
