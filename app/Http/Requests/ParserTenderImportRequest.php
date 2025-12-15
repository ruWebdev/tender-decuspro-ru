<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class ParserTenderImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user && ($user->isAdmin() || $user->isModerator())) {
            return true;
        }

        $token = Setting::get('parser_tender_import_token');

        if (! is_string($token) || $token === '') {
            return false;
        }

        $requestToken = $this->header('X-Parser-Token');

        return is_string($requestToken) && hash_equals($token, $requestToken);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'uuid', 'exists:users,id'],
            'valid_until_days_from_now' => ['nullable', 'integer', 'min:1', 'max:60'],
            'data' => ['required', 'array'],
            'data.items' => ['required', 'array', 'min:1'],
            'data.items.*.id' => ['required', 'integer'],
            'data.items.*.parent_item_id' => ['nullable', 'integer'],
            'data.items.*.position' => ['nullable', 'integer'],
            'data.items.*.part_number' => ['nullable', 'string'],
            'data.items.*.vendor' => ['nullable', 'string'],
            'data.items.*.equipment_type' => ['nullable', 'string'],
            'data.items.*.series' => ['nullable', 'string'],
            'data.items.*.description' => ['nullable', 'string'],
            'data.items.*.quantity' => ['nullable'],
            'data.items.*.supplier_source' => ['nullable', 'string'],
            'data.items.*.supplier_type' => ['nullable', 'string'],
            'data.items.*.supplier_id' => ['nullable'],
            'data.items.*.supplier_name' => ['nullable', 'string'],
            'data.items.*.availability' => ['nullable', 'string'],
            'data.items.*.price' => ['nullable'],
            'data.items.*.price_sale' => ['nullable'],
            'data.items.*.alternatives' => ['nullable', 'array'],
        ];
    }
}
