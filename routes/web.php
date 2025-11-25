<?php

use App\Http\Controllers\Admin\AdminTendersController;
use App\Http\Controllers\Admin\AdminTenderChatController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Admin\AdminSystemLogController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\DeepSeekController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalTotalController;
use App\Http\Controllers\SupplierProfileController;
use App\Http\Controllers\TenderAutofillController;
use App\Http\Controllers\TenderChatController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
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

    // Управление заявками поставщиков заказчиком
    Route::post('/proposals/{proposal}/approve', [ProposalController::class, 'approve'])
        ->name('proposals.approve');
    Route::post('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])
        ->name('proposals.reject');

    Route::get('/tenders/{tender}/comparison', [TenderComparisonController::class, 'index'])
        ->name('tenders.comparison');

    Route::get('/tenders/{tender}/finish', [TenderFinishController::class, 'index'])
        ->name('tenders.finish');

    Route::post('/tenders/{tender}/finish', [TenderFinishController::class, 'finish'])
        ->name('tenders.finish.submit');
});

// Список тендеров доступен всем авторизованным пользователям (роль-специфичное поведение в контроллере)
// (определено выше, здесь дублирование удалено)

Route::get('/tenders/{tender}', [TenderController::class, 'show'])->name('tenders.show');

Route::middleware(['auth', 'role:supplier'])->group(function () {
    Route::get('/tenders/{tender}/participate', [ProposalController::class, 'participate'])
        ->name('proposals.participate');

    Route::post('/tenders/{tender}/proposal', [ProposalController::class, 'store'])
        ->name('proposals.store');

    Route::post('/tenders/{tender}/chat/messages', [TenderChatController::class, 'storeSupplierMessage'])
        ->name('tenders.chat.messages.store');
    Route::put('/proposal/{proposal}', [ProposalController::class, 'update'])
        ->name('proposals.update');

    Route::get('/tenders/{tender}/best-prices', [TenderComparisonController::class, 'bestPrices'])
        ->name('tenders.best-prices');
});

// перенесено в блок поставщика

Route::get('/proposals/{proposal}/total', [ProposalTotalController::class, 'show'])
    ->middleware(['auth'])
    ->name('proposals.total');

Route::middleware(['auth'])->group(function () {
    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet.index');

    Route::middleware(['role:supplier'])->group(function () {
        Route::get('/profile/supplier', [SupplierProfileController::class, 'index'])->name('profile.supplier');

        // Список заявок поставщика
        Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
        // Отзыв заявки
        Route::post('/proposal/{proposal}/withdraw', [ProposalController::class, 'withdraw'])->name('proposals.withdraw');
        // Удаление черновика
        Route::delete('/proposal/{proposal}/discard', [ProposalController::class, 'discard'])->name('proposals.discard');

        // Вопросы по тендерам (поставщики могут задавать)
        Route::post('/tenders/{tender}/questions', [\App\Http\Controllers\TenderQuestionController::class, 'store'])
            ->name('tenders.questions.store');

        Route::post('/profile/supplier/documents', [SupplierProfileController::class, 'uploadDocuments'])
            ->name('profile.supplier.documents.upload');
    });


    Route::middleware(['role:admin|moderator'])->group(function () {
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
            Route::post('/tenders/autofill', [TenderAutofillController::class, 'autofill'])->name('tenders.autofill');
            Route::get('/tenders/{tender}', [AdminTendersController::class, 'show'])->name('tenders.show');
            Route::get('/tenders/{tender}/edit', [AdminTendersController::class, 'edit'])->name('tenders.edit');
            Route::put('/tenders/{tender}', [AdminTendersController::class, 'update'])->name('tenders.update');
            Route::post('/tenders/{tender}/retender', [AdminTendersController::class, 'retender'])->name('tenders.retender');
            Route::delete('/tenders/{tender}', [AdminTendersController::class, 'destroy'])->name('tenders.destroy');

            // Чаты по тендеру (админ)
            Route::post('/tenders/{tender}/chats/{chat}/messages', [AdminTenderChatController::class, 'storeMessage'])->name('tenders.chats.messages.store');
            Route::post('/tenders/{tender}/chats/{chat}/read', [AdminTenderChatController::class, 'markAsRead'])->name('tenders.chats.read');
            Route::post('/tenders/{tender}/chats/{chat}/toggle-translate', [AdminTenderChatController::class, 'toggleTranslate'])->name('tenders.chats.toggle_translate');

            Route::get('/content', [\App\Http\Controllers\Admin\AdminContentController::class, 'index'])->name('content.index');
            Route::post('/content/home/save', [\App\Http\Controllers\Admin\AdminContentController::class, 'saveHome'])->name('content.home.save');
            Route::get('/content/pages', [\App\Http\Controllers\Admin\AdminContentController::class, 'pages'])->name('content.pages');
            Route::get('/content/articles', [\App\Http\Controllers\Admin\AdminContentController::class, 'articles'])->name('content.articles');
            Route::get('/content/news', [\App\Http\Controllers\Admin\AdminContentController::class, 'news'])->name('content.news');

            // Редактирование статических страниц (Пользовательское соглашение, Политика, Регламент)
            Route::get('/content/static-pages', [AdminStaticPagesController::class, 'edit'])->name('content.static_pages');
            Route::post('/content/static-pages', [AdminStaticPagesController::class, 'update'])->name('content.static_pages.update');

            // Инструменты ИИ (только для администратора)
            Route::middleware('role:admin')->group(function () {
                Route::get('/ai', [\App\Http\Controllers\Admin\AdminAIController::class, 'index'])->name('ai.index');
                Route::post('/ai/settings', [\App\Http\Controllers\Admin\AdminAIController::class, 'saveSettings'])->name('ai.save_settings');
                Route::post('/ai/generate-tender', [\App\Http\Controllers\Admin\AdminAIController::class, 'generateTender'])->name('ai.generate_tender');
                Route::post('/ai/translate-tenders', [\App\Http\Controllers\Admin\AdminAIController::class, 'translateTenders'])->name('ai.translate_tenders');

                // Настройки SMTP (только для администратора)
                Route::get('/smtp', [\App\Http\Controllers\Admin\AdminSMTPController::class, 'index'])->name('smtp.index');
                Route::post('/smtp', [\App\Http\Controllers\Admin\AdminSMTPController::class, 'save'])->name('smtp.save');

                // Резервные копии (только для администратора)
                Route::get('/backup', [AdminBackupController::class, 'index'])->name('backup.index');
                Route::post('/backup/run', [AdminBackupController::class, 'run'])->name('backup.run');
                Route::get('/backup/download/{file}', [AdminBackupController::class, 'download'])->name('backup.download');
                Route::delete('/backup/{file}', [AdminBackupController::class, 'destroy'])->name('backup.destroy');

                // Системные логи (только для администратора)
                Route::get('/system-logs', [AdminSystemLogController::class, 'index'])->name('system_logs.index');
            });

            // Модерация вопросов по тендерам
            Route::get('/tenders/{tender}/questions', [\App\Http\Controllers\Admin\AdminTenderQuestionController::class, 'index'])->name('tenders.questions.index');
            Route::post('/tenders/{tender}/questions/{question}/publish', [\App\Http\Controllers\Admin\AdminTenderQuestionController::class, 'publish'])->name('tenders.questions.publish');
            Route::post('/tenders/{tender}/questions/{question}/unpublish', [\App\Http\Controllers\Admin\AdminTenderQuestionController::class, 'unpublish'])->name('tenders.questions.unpublish');
            Route::post('/tenders/{tender}/questions/{question}/answer', [\App\Http\Controllers\Admin\AdminTenderQuestionController::class, 'answer'])->name('tenders.questions.answer');

            // Поставщики (админ)
            Route::get('/suppliers', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'index'])->name('suppliers.index');
            Route::get('/suppliers/{user}', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'show'])->name('suppliers.show');
            Route::get('/suppliers/{user}/documents', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'documents'])->name('suppliers.documents');
            Route::post('/supplier-documents/{document}/approve', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'approveDocument'])->name('suppliers.documents.approve');
            Route::post('/supplier-documents/{document}/reject', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'rejectDocument'])->name('suppliers.documents.reject');
            Route::get('/suppliers/{user}/logs', [\App\Http\Controllers\Admin\AdminSuppliersController::class, 'logs'])->name('suppliers.logs');

            // Заявки (админ)
            Route::get('/applications', [\App\Http\Controllers\Admin\AdminApplicationsController::class, 'index'])->name('applications.index');
            Route::post('/applications/{proposal}/approve', [\App\Http\Controllers\ProposalController::class, 'approve'])->name('applications.approve');
            Route::post('/applications/{proposal}/reject', [\App\Http\Controllers\ProposalController::class, 'reject'])->name('applications.reject');
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
