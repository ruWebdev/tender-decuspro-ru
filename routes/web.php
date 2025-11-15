<?php

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\DeepSeekController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalTotalController;
use App\Http\Controllers\SupplierProfileController;
use App\Http\Controllers\TenderAutofillController;
use App\Http\Controllers\TenderComparisonController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TenderFinishController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [CabinetController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
    Route::get('/tenders/create', [TenderController::class, 'create'])->name('tenders.create');
    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');

    Route::post('/tenders/autofill', [TenderAutofillController::class, 'autofill'])
        ->name('tenders.autofill');

    Route::post('/deepseek/parse', [DeepSeekController::class, 'parse'])->name('deepseek.parse');
    Route::get('/deepseek/result/{jobId}', [DeepSeekController::class, 'result'])->name('deepseek.result');

    Route::get('/tenders/{tender}/proposals', [ProposalController::class, 'indexCustomer'])
        ->name('proposals.index.customer');

    Route::get('/proposals/{proposal}', [ProposalController::class, 'viewCustomer'])
        ->name('proposals.view.customer');

    Route::get('/tenders/{tender}/comparison', [TenderComparisonController::class, 'index'])
        ->name('tenders.comparison');

    Route::get('/tenders/{tender}/finish', [TenderFinishController::class, 'index'])
        ->name('tenders.finish');

    Route::post('/tenders/{tender}/finish', [TenderFinishController::class, 'finish'])
        ->name('tenders.finish.submit');
});

Route::get('/tenders/{tender}', [TenderController::class, 'show'])->name('tenders.show');

Route::middleware(['auth', 'role:supplier'])->group(function () {
    Route::get('/tenders/{tender}/participate', [ProposalController::class, 'participate'])
        ->name('proposals.participate');

    Route::post('/tenders/{tender}/proposal', [ProposalController::class, 'store'])
        ->name('proposals.store');

    Route::put('/proposal/{proposal}', [ProposalController::class, 'update'])
        ->name('proposals.update');

    Route::get('/tenders/{tender}/best-prices', [TenderComparisonController::class, 'bestPrices'])
        ->name('tenders.best-prices');
});

Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');

Route::get('/proposals/{proposal}/total', [ProposalTotalController::class, 'show'])
    ->middleware(['auth'])
    ->name('proposals.total');

Route::middleware(['auth'])->group(function () {
    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet.index');

    Route::middleware(['role:supplier'])->group(function () {
        Route::get('/profile/supplier', [SupplierProfileController::class, 'index'])->name('profile.supplier');
    });
});

Route::get('/lang/{locale}', function (string $locale) {
    session(['locale' => $locale]);

    return back();
})->name('lang.switch');

require __DIR__ . '/auth.php';
