<?php

namespace App\Http\Controllers;

use App\Models\Category;

class ModalController extends Controller
{
    public function getModalIncome()
    {
        $categories = Category::where('type', 'Entrada')->get();
        return view('modals.income', compact('categories'));
    }

    public function getModalDeposit()
    {
        $categories = Category::where('type', 'Aporte')->get();
        return view('modals.deposit', compact('categories'));
    }

    public function getModalWithdrawal()
    {
        $categories = Category::where('type', 'Saída')->get();
        return view('modals.withdrawal', compact('categories'));
    }

    public function getModalExpense()
    {
        $categories = Category::where('type', 'Despesa')->get();
        return view('modals.expense', compact('categories'));
    }

    public function getModalInstallments()
    {
        $categories = Category::all();
        return view('modals.installments', compact('categories'));
    }

    public function getModalCreditCardTransaction($cardId = null)
    {
        $categories = Category::all();

        if ($cardId) {
            $creditCard = auth()->user()->creditCards()->findOrFail($cardId);
            return view('modals.credit-card-transaction', compact('categories', 'creditCard'));
        }

        $creditCards = auth()->user()->creditCards()->get();
        return view('modals.credit-card-transaction', compact('categories', 'creditCards'));
    }

    public function getModalCreateUsers()
    {
        return view('modals.create-user');
    }

    public function getModalApiHelp()
    {
        return view('modals.api-help');
    }    
}