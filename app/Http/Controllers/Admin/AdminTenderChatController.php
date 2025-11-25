<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\TenderChat;
use App\Models\TenderChatMessage;
use App\Services\TranslatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTenderChatController extends Controller
{
    public function storeMessage(Request $request, Tender $tender, TenderChat $chat, TranslatorService $translator): RedirectResponse
    {
        if ($chat->tender_id !== $tender->id) {
            abort(404);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $message = new TenderChatMessage([
            'chat_id' => $chat->id,
            'sender_id' => $tender->customer_id,
            'body' => $data['body'],
            'is_read_by_customer' => true,
            'is_read_by_supplier' => false,
        ]);

        if ($chat->translate_to_ru) {
            $translatedRu = $translator->translate($data['body'], 'ru');

            if (is_string($translatedRu) && $translatedRu !== '') {
                $message->translated_body_ru = $translatedRu;
            }

            $supplier = $chat->supplier()->select('id', 'locale')->first();

            if ($supplier && $supplier->locale) {
                $translatedForSupplier = $translator->translate($data['body'], $supplier->locale);

                if (is_string($translatedForSupplier) && $translatedForSupplier !== '') {
                    $message->translated_body_supplier = $translatedForSupplier;
                }
            }
        }

        $message->save();

        return back();
    }

    public function markAsRead(Tender $tender, TenderChat $chat): RedirectResponse
    {
        if ($chat->tender_id !== $tender->id) {
            abort(404);
        }

        $chat->messages()
            ->where('sender_id', $chat->supplier_id)
            ->where('is_read_by_customer', false)
            ->update(['is_read_by_customer' => true]);

        return back();
    }

    public function toggleTranslate(Tender $tender, TenderChat $chat, TranslatorService $translator): RedirectResponse
    {
        if ($chat->tender_id !== $tender->id) {
            abort(404);
        }

        $chat->translate_to_ru = ! $chat->translate_to_ru;
        $chat->save();

        if ($chat->translate_to_ru) {
            $messages = $chat->messages()->get();
            $supplier = $chat->supplier()->select('id', 'locale')->first();

            foreach ($messages as $message) {
                if ($message->translated_body_ru === null) {
                    $translatedRu = $translator->translate($message->body, 'ru');

                    if (is_string($translatedRu) && $translatedRu !== '') {
                        $message->translated_body_ru = $translatedRu;
                    }
                }

                if ($supplier && $supplier->locale && $message->sender_id === $chat->customer_id && $message->translated_body_supplier === null) {
                    $translatedForSupplier = $translator->translate($message->body, $supplier->locale);

                    if (is_string($translatedForSupplier) && $translatedForSupplier !== '') {
                        $message->translated_body_supplier = $translatedForSupplier;
                    }
                }

                if ($message->isDirty()) {
                    $message->save();
                }
            }
        }

        return back();
    }
}
