<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
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

        $notificationTemplate->update($data);

        return redirect()->route('admin.notification_templates.index');
    }

    public function destroy(NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $notificationTemplate->delete();

        return redirect()->route('admin.notification_templates.index');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'body_ru' => ['required', 'string'],
            'body_en' => ['required', 'string'],
            'body_cn' => ['required', 'string'],
        ]);
    }
}
