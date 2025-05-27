<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;


class ExportController extends Controller
{
    public function exportCsv(DashboardService $dashboardService)
    {
        $user = auth()->user();
        $month = (int) request('month') ?: now()->month;
        $transactions = $dashboardService->getTransactions($user, true, $month);

        $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="transacoes.csv"',
        ];

        $callback = function () use ($transactions) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['Tipo', 'Categoria', 'Data', 'Valor', 'Descrição']);

        foreach ($transactions as $t) {
            fputcsv($file, [
            $t['type'],
            $t['category'] ?? 'Sem categoria',
            $t['dateDisplay'],
            $t['amount'],
            $t['description'] ?? '',
            ]);
        }

        fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(DashboardService $dashboardService)
    {
        $user = auth()->user();
        $month = (int) request('month') ?: now()->month;
        $transactions = $dashboardService->getTransactions($user, true, $month);

        $pdf = Pdf::loadView('exports.transactions', compact('transactions'));
        return $pdf->download('relatorio-transacoes.pdf');
    }
}
