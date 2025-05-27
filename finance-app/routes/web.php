<?php
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\CreditCardTransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\UserManagementController;

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/profile/api-key', [ProfileController::class, 'updateApiKey'])->name('profile.updateApiKey');
    Route::post('/profile/update-favorites', [ProfileController::class, 'updateFavoriteSymbols'])->name('profile.updateFavorites');
    Route::get('/profile/stock', [ProfileController::class, 'showStock'])->name('profile.stock');

    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/block', [UserManagementController::class, 'toggleBlock'])->name('users.block');
    });
    
    Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store');
    Route::post('/deposits', [DepositController::class, 'store'])->name('deposits.store');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
   
    Route::middleware(['auth'])->group(function () {
    Route::post('/recorrente', [RecurringController::class, 'store'])->name('recorrente.store');
    Route::get('/parcelas', [RecurringController::class, 'index'])->name('installments');
    Route::post('/parcelas/{id}/confirmar', [RecurringController::class, 'confirmar'])->name('parcelas.confirmar');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/cartao-de-credito', [CreditCardController::class, 'index'])->name('credit_cards.index');
        Route::get('/cartao-de-credito/create', [CreditCardController::class, 'create'])->name('credit_cards.create');
        Route::post('/cartao-de-credito', [CreditCardController::class, 'store'])->name('credit_cards.store');
        Route::post('/cartao-de-credito-transações', [CreditCardTransactionController::class, 'store'])->name('credit_card_transactions.store');
        Route::get('/cartao-de-credito/{creditCard}', [CreditCardController::class, 'show'])->name('credit_cards.show');
        Route::post('/cartao-de-credito/{creditCard}/pagar-parcela', [CreditCardController::class, 'payInvoice'])->name('credit_cards.pay_invoice');
        Route::post('/cartao-de-credito-transações/{transaction}/pay', [CreditCardController::class, 'payInstallment'])->name('credit_card_transactions.pay');
    }); 

    Route::get('/financeiro', [DashboardController::class, 'finance'])->name('finance');

    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard.index');
    Route::get('/dashboard/monthly-data', [DashboardController::class, 'getMonthlyData'])->name('dashboard.monthly');
    Route::get('/dashboard/balance-history', [DashboardController::class, 'getBalanceHistory'])->name('dashboard.balanceHistory');
    Route::get('/dashboard/expenses-by-category', [DashboardController::class, 'expensesByCategory'])->name('dashboard.expenses.byCategory');
    Route::get('/dashboard/incomes-by-category', [DashboardController::class, 'incomesByCategory'])->name('dashboard.incomes.byCategory');
    Route::get('/dashboard/aportes-vs-saidas', [DashboardController::class, 'aportesVsSaidas'])->name('dashboard.aportes.vsSaidas');
    Route::get('/dashboard/daily-movements', [DashboardController::class, 'dailyMovements'])->name('dashboard.dailyMovements');

    Route::prefix('modal')->name('modal.')->group(function () {
        Route::get('/income', [ModalController::class, 'getModalIncome'])->name('income');
        Route::get('/deposit', [ModalController::class, 'getModalDeposit'])->name('deposit');
        Route::get('/withdrawal', [ModalController::class, 'getModalWithdrawal'])->name('withdrawal');
        Route::get('/expense', [ModalController::class, 'getModalExpense'])->name('expense');
        Route::get('/parcelado', [ModalController::class, 'getModalInstallments'])->name('installments');
        Route::get('/credit-card/{card?}', [ModalController::class, 'getModalCreditCardTransaction'])->name('credit_card');
        Route::get('/api-help', [ModalController::class, 'getModalApiHelp'])->name('api_help');
        Route::get('/create/user', [ModalController::class, 'getModalCreateUsers'])->name('create_user');
    });

    Route::get('/api/stocks', [StockController::class, 'getQuotes'])->name('stocks.api');
    Route::get('/api/search-company', [StockController::class, 'searchCompany'])->name('stocks.searchCompany');
});