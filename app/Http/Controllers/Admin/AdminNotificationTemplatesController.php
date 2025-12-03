<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Services\NotificationTemplateTranslateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminNotificationTemplatesController extends Controller
{
    public function index(): Response
    {
        $templates = NotificationTemplate::query()
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return Inertia::render('Admin/NotificationTemplates/Index', [
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/NotificationTemplates/Edit', [
            'template' => null,
            'types' => NotificationTemplate::types(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['needs_translation'] = true;

        NotificationTemplate::create($data);

        return redirect()->route('admin.notification_templates.index');
    }

    public function edit(NotificationTemplate $notificationTemplate): Response
    {
        return Inertia::render('Admin/NotificationTemplates/Edit', [
            'template' => [
                'id' => $notificationTemplate->id,
                'name' => $notificationTemplate->name,
                'type' => $notificationTemplate->type,
                'body_ru' => $notificationTemplate->body_ru,
                'body_en' => $notificationTemplate->body_en,
                'body_cn' => $notificationTemplate->body_cn,
            ],
            'types' => NotificationTemplate::types(),
        ]);
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['needs_translation'] = true;

        $notificationTemplate->update($data);

        return redirect()->route('admin.notification_templates.index');
    }

    public function destroy(NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $notificationTemplate->delete();

        return redirect()->route('admin.notification_templates.index');
    }

    public function translate(Request $request, NotificationTemplateTranslateService $translator): JsonResponse
    {
        $data = $request->validate([
            'body_ru' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'body_cn' => ['nullable', 'string'],
        ]);

        $result = $translator->translate(
            $data['body_ru'] ?? null,
            $data['body_en'] ?? null,
            $data['body_cn'] ?? null,
        );

        if (! $result) {
            return response()->json([
                'success' => false,
                'error' => 'translation_failed',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'body_ru' => $result['body_ru'],
            'body_en' => $result['body_en'],
            'body_cn' => $result['body_cn'],
        ]);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'body_ru' => ['required', 'string'],
            'body_en' => ['nullable', 'string'],
            'body_cn' => ['nullable', 'string'],
        ]);
    }
}
