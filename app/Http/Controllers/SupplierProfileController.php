<?php

namespace App\Http\Controllers;

use App\Models\SupplierDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SupplierProfileController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/Supplier');
    }

    public function uploadDocuments(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! (method_exists($user, 'isSupplier') && $user->isSupplier())) {
            abort(403);
        }

        $documentTypes = [
            'business_license',
            'tax_certificate',
            'power_of_attorney',
            'board_resolution',
            'passport_director',
            'passport_signatory',
        ];

        $rules = [];
        foreach ($documentTypes as $type) {
            $rules[$type] = ['nullable', 'file', 'max:10240'];
        }

        $validated = $request->validate($rules);

        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);

                // Удаляем старые отклонённые документы этого типа вместе с файлами
                $rejectedDocs = SupplierDocument::query()
                    ->where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('status', 'rejected')
                    ->get();

                foreach ($rejectedDocs as $doc) {
                    if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                        Storage::disk('public')->delete($doc->file_path);
                    }

                    $doc->delete();
                }

                $path = $file->store('supplier_documents/' . $user->id, 'public');

                SupplierDocument::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'file_path' => $path,
                    'status' => 'pending',
                ]);
            }
        }

        return back();
    }
}
