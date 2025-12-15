<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParserTenderImportRequest;
use App\Jobs\GenerateTenderRuSummaryJob;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\TenderItemSupplierOffer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ParserTenderImportController extends Controller
{
    public function store(ParserTenderImportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customerId = $validated['customer_id'] ?? null;

        $customer = $customerId
            ? User::query()->whereKey($customerId)->first()
            : User::role(User::ROLE_CUSTOMER)->orderBy('id')->first();

        if (! $customer) {
            return response()->json([
                'message' => 'Не найден заказчик для создания тендера',
            ], 422);
        }

        $days = isset($validated['valid_until_days_from_now'])
            ? (int) $validated['valid_until_days_from_now']
            : 7;

        if ($days < 1) {
            $days = 7;
        }

        if ($days > 60) {
            $days = 60;
        }

        $payloadItems = $validated['data']['items'] ?? [];

        $tender = null;

        DB::transaction(function () use ($customer, $days, $payloadItems, &$tender): void {
            $tender = Tender::create([
                'customer_id' => $customer->id,
                'title' => 'Тендер из импорта',
                'description' => null,
                'hidden_comment' => null,
                'valid_until' => now()->addDays($days),
                'status' => 'open',
            ]);

            $index = 0;

            foreach ($payloadItems as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $parentItemId = $row['parent_item_id'] ?? null;

                if ($parentItemId !== null) {
                    continue;
                }

                $title = isset($row['description']) ? trim((string) $row['description']) : '';

                if ($title === '') {
                    $equipmentType = isset($row['equipment_type']) ? trim((string) $row['equipment_type']) : '';
                    $vendor = isset($row['vendor']) ? trim((string) $row['vendor']) : '';
                    $partNumber = isset($row['part_number']) ? trim((string) $row['part_number']) : '';

                    $title = trim(implode(' ', array_filter([$equipmentType, $vendor, $partNumber])));
                }

                if ($title === '') {
                    $index++;

                    continue;
                }

                $quantityRaw = $row['quantity'] ?? 1;
                $quantity = is_numeric($quantityRaw) ? (float) $quantityRaw : 1.0;

                if ($quantity <= 0) {
                    $quantity = 1.0;
                }

                $positionIndex = isset($row['position']) && is_numeric($row['position'])
                    ? max(0, (int) $row['position'] - 1)
                    : $index;

                $itemMeta = $row;
                unset($itemMeta['alternatives']);

                /** @var TenderItem $tenderItem */
                $tenderItem = $tender->items()->create([
                    'title' => $title,
                    'quantity' => $quantity,
                    'unit' => null,
                    'meta' => $itemMeta,
                    'position_index' => $positionIndex,
                ]);

                $this->storeOfferForRow($tenderItem, $row);

                $alternatives = $row['alternatives'] ?? [];

                if (is_array($alternatives)) {
                    foreach ($alternatives as $alt) {
                        if (! is_array($alt)) {
                            continue;
                        }

                        $this->storeOfferForRow($tenderItem, $alt);
                    }
                }

                $index++;
            }
        });

        if (! $tender) {
            return response()->json([
                'message' => 'Не удалось создать тендер',
            ], 500);
        }

        GenerateTenderRuSummaryJob::dispatch($tender);

        return response()->json([
            'tender_id' => $tender->id,
        ]);
    }

    private function storeOfferForRow(TenderItem $tenderItem, array $row): void
    {
        $price = $this->toDecimalOrNull($row['price'] ?? null);
        $priceSale = $this->toDecimalOrNull($row['price_sale'] ?? null);

        $supplierName = isset($row['supplier_name']) ? trim((string) $row['supplier_name']) : null;
        $availability = isset($row['availability']) ? trim((string) $row['availability']) : null;
        $supplierSource = isset($row['supplier_source']) ? trim((string) $row['supplier_source']) : null;
        $supplierType = isset($row['supplier_type']) ? trim((string) $row['supplier_type']) : null;

        $hasAnyData = ($supplierName !== null && $supplierName !== '')
            || ($availability !== null && $availability !== '')
            || $price !== null
            || $priceSale !== null
            || ($supplierSource !== null && $supplierSource !== '')
            || ($supplierType !== null && $supplierType !== '');

        if (! $hasAnyData) {
            return;
        }

        $meta = $row;
        unset($meta['alternatives']);

        TenderItemSupplierOffer::create([
            'tender_item_id' => $tenderItem->id,
            'external_item_id' => isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null,
            'external_parent_item_id' => isset($row['parent_item_id']) && is_numeric($row['parent_item_id']) ? (int) $row['parent_item_id'] : null,
            'supplier_source' => $supplierSource !== '' ? $supplierSource : null,
            'supplier_type' => $supplierType !== '' ? $supplierType : null,
            'supplier_external_id' => isset($row['supplier_id']) ? (string) $row['supplier_id'] : null,
            'supplier_name' => $supplierName !== '' ? $supplierName : null,
            'availability' => $availability !== '' ? $availability : null,
            'price' => $price,
            'price_sale' => $priceSale,
            'currency' => 'RUB',
            'meta' => $meta,
        ]);
    }

    private function toDecimalOrNull(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
