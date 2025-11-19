<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\TenderQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminTenderQuestionController extends Controller
{
    public function index(Request $request, Tender $tender): Response
    {
        $this->authorize('update', $tender);

        $questions = $tender->questions()
            ->with(['user:id,name'])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/Tenders/Questions', [
            'tender' => $tender->only(['id', 'title']),
            'questions' => $questions,
        ]);
    }

    public function publish(Request $request, Tender $tender, TenderQuestion $question): RedirectResponse
    {
        $this->authorize('update', $tender);
        if ($question->tender_id !== $tender->id) {
            abort(404);
        }
        $question->update(['is_public' => true]);

        return back()->with('success', 'Вопрос опубликован');
    }

    public function unpublish(Request $request, Tender $tender, TenderQuestion $question): RedirectResponse
    {
        $this->authorize('update', $tender);
        if ($question->tender_id !== $tender->id) {
            abort(404);
        }
        $question->update(['is_public' => false]);

        return back()->with('success', 'Вопрос снят с публикации');
    }

    public function answer(Request $request, Tender $tender, TenderQuestion $question): RedirectResponse
    {
        $this->authorize('update', $tender);
        if ($question->tender_id !== $tender->id) {
            abort(404);
        }

        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $question->update([
            'answer' => trim($validated['answer']),
        ]);

        return back()->with('success', 'Ответ сохранен');
    }
}
