<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;

class IncomeController extends Controller
{
    public function store(Request $request)
    {
    $request->validate([
        'expense_date' => 'required|date',
        'amount' => 'required|numeric',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
    ]);

    $wallet = auth()->user()->wallet;

    Income::create([
        'wallet_id' => $wallet->id,
        'expense_date' => $request->expense_date,
        'amount' => $request->amount,
        'description' => $request->description,
        'category_id' => $request->category_id,
    ]);

    return redirect()->back()->with('success', 'Entrada adicionada com sucesso!');
    }
}
