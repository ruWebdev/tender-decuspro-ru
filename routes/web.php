<?php

use App\Http\Controllers\Admin\AdminTendersController;
use App\Http\Controllers\Admin\AdminUsersController;
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
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\Admin\AdminStaticPagesController;
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

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', \App\Http\Controllers\Admin\AdminDashboardController::class)->name('admin.dashboard');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUsersController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUsersController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUsersController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUsersController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/block', [AdminUsersController::class, 'block'])->name('users.block');
            Route::post('/users/{user}/unblock', [AdminUsersController::class, 'unblock'])->name('users.unblock');
            Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');

            Route::get('/tenders', [AdminTendersController::class, 'index'])->name('tenders.index');
            Route::get('/tenders/create', [AdminTendersController::class, 'create'])->name('tenders.create');
            Route::post('/tenders', [AdminTendersController::class, 'store'])->name('tenders.store');
            Route::get('/tenders/{tender}', [AdminTendersController::class, 'show'])->name('tenders.show');
            Route::get('/tenders/{tender}/edit', [AdminTendersController::class, 'edit'])->name('tenders.edit');
            Route::put('/tenders/{tender}', [AdminTendersController::class, 'update'])->name('tenders.update');
            Route::delete('/tenders/{tender}', [AdminTendersController::class, 'destroy'])->name('tenders.destroy');

            Route::get('/content', [\App\Http\Controllers\Admin\AdminContentController::class, 'index'])->name('content.index');
            Route::post('/content/home/save', [\App\Http\Controllers\Admin\AdminContentController::class, 'saveHome'])->name('content.home.save');
            Route::get('/content/pages', [\App\Http\Controllers\Admin\AdminContentController::class, 'pages'])->name('content.pages');
            Route::get('/content/articles', [\App\Http\Controllers\Admin\AdminContentController::class, 'articles'])->name('content.articles');
            Route::get('/content/news', [\App\Http\Controllers\Admin\AdminContentController::class, 'news'])->name('content.news');

            // Редактирование статических страниц (Пользовательское соглашение, Политика, Регламент)
            Route::get('/content/static-pages', [AdminStaticPagesController::class, 'edit'])->name('content.static_pages');
            Route::post('/content/static-pages', [AdminStaticPagesController::class, 'update'])->name('content.static_pages.update');
        });
    });
});

Route::get('/lang/{locale}', function (string $locale) {
    session(['locale' => $locale]);

    return back();
})->name('lang.switch');

// Публичные документы
Route::get('/docs/{slug}', [ContentPageController::class, 'show'])
    ->whereIn('slug', ['user-agreement', 'privacy-policy', 'procurement-rules'])
    ->name('docs.show');

require __DIR__ . '/auth.php';
