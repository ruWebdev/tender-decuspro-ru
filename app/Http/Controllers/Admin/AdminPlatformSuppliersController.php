<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mailing;
use App\Models\NotificationTemplate;
use App\Models\PlatformSupplier;
use App\Models\Tender;
use App\Services\SmtpBzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class AdminPlatformSuppliersController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PlatformSupplier::query()
            ->select('id', 'name', 'phone', 'email', 'website', 'comment', 'language', 'invitation_sent', 'created_at');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $suppliers = $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/PlatformSuppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $request->input('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/PlatformSuppliers/Edit', [
            'supplier' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:5'],
            'invitation_sent' => ['nullable', 'boolean'],
        ]);

        PlatformSupplier::create($data);

        return redirect()->route('admin.platform_suppliers.index');
    }

    public function edit(PlatformSupplier $platformSupplier): Response
    {
        return Inertia::render('Admin/PlatformSuppliers/Edit', [
            'supplier' => [
                'id' => $platformSupplier->id,
                'name' => $platformSupplier->name,
                'phone' => $platformSupplier->phone,
                'email' => $platformSupplier->email,
                'website' => $platformSupplier->website,
                'comment' => $platformSupplier->comment,
                'language' => $platformSupplier->language,
                'invitation_sent' => (bool) $platformSupplier->invitation_sent,
            ],
        ]);
    }

    public function update(Request $request, PlatformSupplier $platformSupplier): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:5'],
            'invitation_sent' => ['nullable', 'boolean'],
        ]);

        $platformSupplier->update($data);

        return redirect()->route('admin.platform_suppliers.index');
    }

    public function destroy(PlatformSupplier $platformSupplier): RedirectResponse
    {
        $platformSupplier->delete();

        return redirect()->route('admin.platform_suppliers.index');
    }

    public function mailing(): Response
    {
        $mailings = Mailing::with('notificationTemplate')
            ->orderByDesc('created_at')
            ->paginate(20);

        $templates = NotificationTemplate::select('id', 'name', 'type')->get();

        $tenders = Tender::select('id', 'title')
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        // Подсчитываем потенциальных получателей
        $totalSuppliers = PlatformSupplier::whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

        return Inertia::render('Admin/PlatformSuppliers/Mailing', [
            'mailings' => $mailings,
            'templates' => $templates,
            'tenders' => $tenders,
            'total_suppliers' => $totalSuppliers,
        ]);
    }

    public function storeMailing(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'emails_limit' => ['required', 'integer', 'min:1'],
            'notification_template_id' => ['required', 'uuid', 'exists:notification_templates,id'],
            'tender_ids' => ['nullable', 'array'],
            'tender_ids.*' => ['uuid', 'exists:tenders,id'],
            'company_filter' => ['nullable', 'string', 'max:1000'],
        ]);

        // Подсчитываем потенциальных получателей
        $query = PlatformSupplier::whereNotNull('email')
            ->where('email', '!=', '');

        if (! empty($data['company_filter'])) {
            $keywords = array_map('trim', explode(',', $data['company_filter']));
            $keywords = array_filter($keywords);

            if (! empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('name', 'LIKE', '%' . $keyword . '%');
                    }
                });
            }
        }

        $totalRecipients = $query->count();

        Mailing::create([
            'name' => $data['name'],
            'emails_limit' => $data['emails_limit'],
            'notification_template_id' => $data['notification_template_id'],
            'tender_ids' => $data['tender_ids'] ?? [],
            'company_filter' => $data['company_filter'] ?? null,
            'status' => Mailing::STATUS_DRAFT,
            'total_recipients' => $totalRecipients,
        ]);

        return redirect()->route('admin.platform_suppliers.mailing')
            ->with('success', __('admin.platform_suppliers.mailing.created'));
    }

    public function startMailing(Mailing $mailing): RedirectResponse
    {
        if (! $mailing->canStart()) {
            return back()->with('error', __('admin.platform_suppliers.mailing.cannot_start'));
        }

        $mailing->update(['status' => Mailing::STATUS_RUNNING]);

        return back()->with('success', __('admin.platform_suppliers.mailing.started'));
    }

    public function stopMailing(Mailing $mailing): RedirectResponse
    {
        if (! $mailing->canStop()) {
            return back()->with('error', __('admin.platform_suppliers.mailing.cannot_stop'));
        }

        $mailing->update(['status' => Mailing::STATUS_PAUSED]);

        return back()->with('success', __('admin.platform_suppliers.mailing.stopped'));
    }

    public function destroyMailing(Mailing $mailing): RedirectResponse
    {
        $mailing->delete();

        return redirect()->route('admin.platform_suppliers.mailing')
            ->with('success', __('admin.platform_suppliers.mailing.deleted'));
    }

    public function sendInvitation(PlatformSupplier $platformSupplier, SmtpBzService $smtpBzService): RedirectResponse
    {
        if (! $platformSupplier->email) {
            return back()->with('error', __('admin.platform_suppliers.invitation.no_email'));
        }

        $locale = $platformSupplier->language ?: 'ru';

        if (! in_array($locale, ['ru', 'en', 'cn'], true)) {
            $locale = 'ru';
        }

        $template = NotificationTemplate::query()
            ->where('type', NotificationTemplate::TYPE_PLATFORM_INVITATION)
            ->first();

        if (! $template) {
            return back()->with('error', __('admin.platform_suppliers.invitation.no_template'));
        }

        $body = match ($locale) {
            'en' => $template->body_en ?: ($template->body_ru ?? ''),
            'cn' => $template->body_cn ?: ($template->body_ru ?? ''),
            default => $template->body_ru ?? '',
        };

        if (trim($body) === '') {
            return back()->with('error', __('admin.platform_suppliers.invitation.empty_body'));
        }

        $subject = trans('admin.notification_templates.types.platform_invitation', [], $locale);

        $html = '<html><body><pre style="font-family: inherit; white-space: pre-wrap;">' . e($body) . '</pre></body></html>';

        $success = false;

        if ($smtpBzService->hasApiKey()) {
            $result = $smtpBzService->send(
                $platformSupplier->email,
                $subject,
                $html,
                $body,
            );

            $success = $result['success'] ?? false;

            if (! $success) {
                Log::error('Platform supplier invitation via SMTP.bz failed', [
                    'supplier_id' => $platformSupplier->id,
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                ]);
            }
        } else {
            try {
                Mail::send([], [], function ($message) use ($platformSupplier, $subject, $html, $body): void {
                    $message->to($platformSupplier->email)
                        ->subject($subject)
                        ->setBody($html, 'text/html');

                    if (trim($body) !== '') {
                        $message->text($body);
                    }
                });

                $success = true;
            } catch (\Throwable $e) {
                Log::error('Platform supplier invitation via SMTP failed', [
                    'supplier_id' => $platformSupplier->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($success) {
            $platformSupplier->invitation_sent = true;
            $platformSupplier->save();

            return back()->with('success', __('admin.platform_suppliers.invitation.sent_success'));
        }

        return back()->with('error', __('admin.platform_suppliers.invitation.sent_error'));
    }
}
