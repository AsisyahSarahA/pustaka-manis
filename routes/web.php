<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorLogController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::get('/', fn () => redirect()->route('login'))->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Kiosk — tanpa autentikasi
Route::prefix('kiosk')->name('kiosk.')->middleware('module:visitor')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/checkin', [KioskController::class, 'checkin'])->name('checkin')->middleware('throttle:10,1');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('role:admin,pustakawan,viewer')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart');
        Route::get('/api/search', [GlobalSearchController::class, 'search'])->name('api.search');
        Route::get('/api/notifications', [NotificationController::class, 'index'])->name('api.notifications');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('module:report');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export')->middleware('module:report');
        Route::get('reports/export-skk/{loanItem}', [ReportController::class, 'exportLostBookWord'])->name('reports.export.skk')->middleware('module:report');
    });

    Route::middleware('role:admin,pustakawan')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('books', BookController::class);
        Route::get('books-import', [BookController::class, 'importForm'])->name('books.import');
        Route::post('books-import', [BookController::class, 'import'])->name('books.import.process');
        Route::get('books-template', [BookController::class, 'downloadTemplate'])->name('books.template');
        Route::get('books/{book}/items', [BookItemController::class, 'index'])->name('book-items.index');
        Route::get('books/{book}/labels', [BookItemController::class, 'printLabels'])->name('books.labels');
        Route::get('book-items/{bookItem}/label', [BookItemController::class, 'printSingleLabel'])->name('book-items.label');
        Route::put('book-items/{bookItem}', [BookItemController::class, 'update'])->name('book-items.update');
        Route::delete('book-items/{bookItem}', [BookItemController::class, 'destroy'])->name('book-items.destroy');
        Route::resource('members', MemberController::class);
        Route::get('members/{member}/card', [MemberController::class, 'printCard'])->name('members.card')->middleware('module:member_card');

        Route::get('loans/borrow', [LoanController::class, 'borrow'])->name('loans.borrow');
        Route::post('loans/borrow', [LoanController::class, 'store'])->name('loans.store');
        Route::get('loans/api/member', [LoanController::class, 'searchMember'])->name('loans.api.member');
        Route::get('loans/api/book', [LoanController::class, 'searchBook'])->name('loans.api.book');
        Route::get('loans/{loan}/receipt', [LoanController::class, 'receipt'])->name('loans.receipt');
        Route::get('loans/return', [ReturnController::class, 'index'])->name('loans.return');
        Route::post('loans/return', [ReturnController::class, 'store'])->name('loans.return.store');
        Route::get('loans/return/api/member', [ReturnController::class, 'searchMember'])->name('loans.return.api.member');
        Route::get('loans', [LoanController::class, 'index'])->name('loans.index');

        // Fine Cashier Routes
        Route::get('fines', [\App\Http\Controllers\FineController::class, 'index'])->name('fines.index');
        Route::post('fines/{fine}/pay', [\App\Http\Controllers\FineController::class, 'pay'])->name('fines.pay');
        Route::post('fines/{fine}/waive', [\App\Http\Controllers\FineController::class, 'waive'])->name('fines.waive');
        Route::get('fines/{fine}/receipt', [\App\Http\Controllers\FineController::class, 'receipt'])->name('fines.receipt');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
