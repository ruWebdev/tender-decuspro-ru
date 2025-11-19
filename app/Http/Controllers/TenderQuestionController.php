<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenderQuestionController extends Controller
{
    public function store(Request $request, Tender $tender): RedirectResponse
    {
        $this->authorize('participate', $tender); // поставщик имеет право задавать вопросы к открытому тендеру

        $user = $request->user();
        if (! $user || $user->role !== 'supplier') {
            abort(403);
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        TenderQuestion::create([
            'tender_id' => $tender->id,
            'user_id' => $user->id,
            'question' => trim($validated['question']),
            'is_public' => false, // публикует администратор
        ]);

        return back()->with('success', 'Вопрос отправлен на модерацию.');
    }
}
