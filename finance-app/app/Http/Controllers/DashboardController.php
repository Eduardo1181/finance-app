<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getMonthlyData(Request $request, DashboardService $dashboardService)
    {
        $user = auth()->user();
        $month = (int) $request->input('month');

        if (!$month || $month < 1 || $month > 12) {
            $month = now()->month;
        }

        $data = $dashboardService->getMonthlyData($user, $month);
        return response()->json($data);
    }

    public function index(DashboardService $dashboardService)
    {
        $user = auth()->user();

        $data = $dashboardService->getMonthlyData($user, now()->month);

        $months = collect(range(1, 12))->map(function ($month) {
            return [
            'value' => $month,
            'label' => ucfirst(\Carbon\Carbon::create()->month($month)->translatedFormat('F')),
            'selected' => now()->month == $month,
            ];
        });

        return view('home', array_merge($data, [
            'months' => $months,
        ]));
    }

    public function dashboard(DashboardService $dashboardService)
    {
        $user = auth()->user();
        $balanceHistory = $dashboardService->getBalanceHistory($user, 6);

        return view('dashboard.index', [
            'balanceHistory' => $balanceHistory,
        ]);
    }

    public function getBalanceHistory(Request $request, DashboardService $dashboardService)
    {
        $user = auth()->user();
        $months = (int) $request->input('months', 6);

        $data = $dashboardService->getBalanceHistory($user, $months);

        return response()->json($data);
    }

    public function finance(DashboardService $dashboardService)
    {
        $user = auth()->user();
        
        $data = $dashboardService->getDashboardData($user);

        $transactions = $dashboardService->getTransactions($user, false);

        $totalIncomes = $data['totalIncomes'] ?? '0,00';
        $totalDeposits = $data['totalDeposits'] ?? '0,00';
        $totalWithdrawals = $data['totalWithdrawals'] ?? '0,00';
        $totalExpenses = $data['totalExpenses'] ?? '0,00';
        $walletBalance = $data['walletBalance'] ?? '0,00';
        $totalCreditcard = $data['totalCreditcard'] ?? '0,00';
        $installments = $dashboardService->getTotalParcelado($user);


        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => ucfirst(Carbon::create()->month($month)->translatedFormat('F')),
                'selected' => now()->month == $month,
            ];
        });

        return view('finance', [
            'transactions' => $transactions,
            'months' => $months,
            'totalIncomes' => $totalIncomes,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalExpenses' => $totalExpenses,
            'walletBalance' => $walletBalance,
            'installments' => $installments,
            'totalCreditcard' => $totalCreditcard,
        ]);
    }

    public function expensesByCategory(Request $request, DashboardService $dashboardService)
    {
        $month = (int) $request->input('month', now()->month);
        $user = auth()->user();

        $data = $dashboardService->getExpensesGroupedByCategory($user, $month);

        return response()->json($data);
    }

    public function incomesByCategory(Request $request, DashboardService $dashboardService)
    {
        $month = (int) $request->input('month', now()->month);
        $user = auth()->user();

        $data = $dashboardService->getIncomesGroupedByCategory($user, $month);

        return response()->json($data);
    }

    public function aportesVsSaidas(Request $request, DashboardService $dashboardService)
    {
        $month = (int) $request->input('month', now()->month);
        $user = auth()->user();

        $data = $dashboardService->getAportesVsSaidas($user, $month);

        return response()->json($data);
    }

    public function dailyMovements(Request $request, DashboardService $dashboardService)
    {
        $month = (int) $request->input('month', now()->month);
        $user = auth()->user();

        return response()->json(
            $dashboardService->getDailyMovements($user, $month)
        );
    }
}