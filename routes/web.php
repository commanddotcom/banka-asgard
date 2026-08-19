<?php

use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\DonorController;
use App\Http\Controllers\Admin\DonorNameController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/reports', 'reports')->name('reports');

Route::get('/banks/{bank}', [TopupController::class, 'create'])->name('banks.topup');
Route::post('/banks/{bank}/invoices', [TopupController::class, 'store'])->name('banks.invoices.store');
Route::get('/invoices/{invoice}', [TopupController::class, 'show'])->name('invoices.show');

Route::middleware('guest')->group(function () {
    Route::get('/login_', [LoginController::class, 'create'])->name('login');
    Route::post('/login_', [LoginController::class, 'store'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin_')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.invoices.index'));

    Route::post('banks/wallets', [BankController::class, 'wallets'])->name('banks.wallets');
    Route::resource('banks', BankController::class)->except(['show', 'destroy']);

    Route::resource('names', DonorNameController::class)
        ->parameters(['names' => 'donorName'])
        ->except(['show']);

    Route::get('donors', [DonorController::class, 'index'])->name('donors.index');
    Route::post('donors/import', [DonorController::class, 'import'])->name('donors.import');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');

    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/comments', [InvoiceController::class, 'storeComment'])->name('invoices.comments.store');
});
