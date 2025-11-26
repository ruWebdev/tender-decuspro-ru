<?php

namespace App\Http\Controllers;

use App\Models\PlatformSupplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParserPlatformSuppliersController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
        ]);

        $query = PlatformSupplier::query();

        $query->where('name', $validated['name']);

        if (! empty($validated['website'])) {
            $query->orWhere('website', $validated['website']);
        }

        if (! empty($validated['email'])) {
            $query->orWhere('email', $validated['email']);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $supplier = PlatformSupplier::query()
            ->where('name', $validated['name'])
            ->when(! empty($validated['website']), function ($query) use ($validated) {
                $query->orWhere('website', $validated['website']);
            })
            ->when(! empty($validated['email']), function ($query) use ($validated) {
                $query->orWhere('email', $validated['email']);
            })
            ->first();

        if (! $supplier) {
            $supplier = PlatformSupplier::create($validated);
        }

        return response()->json([
            'id' => $supplier->id,
            'created' => $supplier->wasRecentlyCreated,
        ]);
    }
}
