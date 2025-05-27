<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditCardTransaction;
use Carbon\Carbon;

class CreditCardTransactionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'credit_card_id'      => 'required|exists:credit_cards,id',
            'category_id'         => 'nullable|exists:categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'purchase_date'       => 'required|date',
            'description'         => 'nullable|string',
            'is_installment'      => 'nullable|boolean',
            'total_installments'  => 'nullable|integer|min:1',
        ]);

        $isInstallment = $data['is_installment'] ?? false;

        if ($isInstallment && ($data['total_installments'] ?? 1) > 1) {
            $total = $data['total_installments'];
            $baseDate = Carbon::parse($data['purchase_date']);

            for ($i = 1; $i <= $total; $i++) {
                CreditCardTransaction::create([
                    'credit_card_id'      => $data['credit_card_id'],
                    'category_id'         => $data['category_id'] ?? null,
                    'amount'              => $data['amount'],
                    'purchase_date'       => $baseDate->copy()->addMonthsNoOverflow($i - 1),
                    'description'         => $data['description'] ?? null,
                    'is_installment'      => true,
                    'installment_number'  => $i,
                    'total_installments'  => $total,
                ]);
            }
        } else {
            CreditCardTransaction::create([
                'credit_card_id' => $data['credit_card_id'],
                'category_id'    => $data['category_id'] ?? null,
                'amount'         => $data['amount'],
                'purchase_date'  => $data['purchase_date'],
                'description'    => $data['description'] ?? null,
                'is_installment' => false,
            ]);
        }

        return redirect()->route('finance');
    }
}
