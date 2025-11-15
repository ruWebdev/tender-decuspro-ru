<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TenderAutofillController extends Controller
{
    /**
     * Автозаполнение позиций тендера через DeepSeek API.
     */
    public function autofill(Request $request): JsonResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string'],
        ]);

        $token = config('services.deepseek.token');
        $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');

        if (! $token) {
            return response()->json(['items' => []]);
        }

        $prompt = "Parse the following list of goods and services.\n" .
            "For each item, extract the name (title) and quantity (quantity).\n" .
            "Return strictly a JSON array of objects in format [{\"title\":\"...\",\"quantity\":\"...\",\"unit\":null}], without comments or additional text.\n\n" .
            $data['text'];

        $items = [];

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post("{$baseUrl}/v1/chat/completions", [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 1024,
                ]);

            if ($response->failed()) {
                return response()->json(['items' => []]);
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || $content === '') {
                return response()->json(['items' => []]);
            }

            // Попытаемся извлечь JSON из ответа
            $jsonMatch = preg_match('/\[.*\]/s', $content, $matches);

            if (! $jsonMatch || ! isset($matches[0])) {
                return response()->json(['items' => []]);
            }

            $decoded = json_decode($matches[0], true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return response()->json(['items' => []]);
            }

            foreach ($decoded as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $title = isset($row['title']) ? (string) $row['title'] : '';
                $quantity = isset($row['quantity']) ? (string) $row['quantity'] : '';
                $unit = isset($row['unit']) ? (string) $row['unit'] : null;

                if ($title === '') {
                    continue;
                }

                $items[] = [
                    'title' => $title,
                    'quantity' => $quantity,
                    'unit' => $unit,
                ];
            }
        } catch (\Throwable $e) {
            return response()->json(['items' => []]);
        }

        return response()->json([
            'items' => $items,
        ]);
    }
}
