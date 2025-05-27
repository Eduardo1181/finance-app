<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;

class DepositController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'monthly_rate' => 'nullable|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'is_locked' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $wallet = auth()->user()->wallet;
        
        Deposit::create([
            'wallet_id' => $wallet->id,
            'expense_date' => $request->expense_date,
            'amount' => $request->amount,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'monthly_rate' => $request->monthly_rate,
            'duration_months' => $request->duration_months,
            'start_date' => $request->start_date ?? $request->expense_date,
            'is_locked' => $request->boolean('is_locked'),
        ]);
        return redirect()->back()->with('success', 'Aporte cadastrado com sucesso!');
    }
}