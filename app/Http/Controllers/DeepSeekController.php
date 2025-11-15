<?php

namespace App\Http\Controllers;

use App\Jobs\DeepSeekParseJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DeepSeekController extends Controller
{
    public function parse(Request $request): JsonResponse
    {
        $data = $request->validate([
            'raw_text' => ['required', 'string'],
        ]);

        $jobId = (string) Str::uuid();

        DeepSeekParseJob::dispatch($data['raw_text'], $jobId);

        return response()->json([
            'jobId' => $jobId,
        ]);
    }

    public function result(string $jobId): JsonResponse
    {
        $items = Cache::get("deepseek_result_{$jobId}", []);

        return response()->json($items);
    }
}
