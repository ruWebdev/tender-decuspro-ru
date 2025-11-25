<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderChat;
use App\Models\TenderChatMessage;
use App\Services\TranslatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenderChatController extends Controller
{
    public function storeSupplierMessage(Request $request, Tender $tender, TranslatorService $translator): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isSupplier') || ! $user->isSupplier()) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $chat = TenderChat::firstOrCreate(
            [
                'tender_id' => $tender->id,
                'supplier_id' => $user->id,
            ],
            [
                'customer_id' => $tender->customer_id,
            ],
        );

        $message = new TenderChatMessage([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'body' => $data['body'],
            'is_read_by_customer' => false,
            'is_read_by_supplier' => true,
        ]);

        if ($chat->translate_to_ru) {
            $translated = $translator->translate($data['body'], 'ru');

            if (is_string($translated) && $translated !== '') {
                $message->translated_body_ru = $translated;
            }
        }

        $message->save();

        return back();
    }
}
