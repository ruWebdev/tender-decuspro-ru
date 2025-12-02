<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSupplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
