<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Validation\ValidationException;

class ItemBarcodeService
{
    public function normalize(string $code): string
    {
        $code = trim($code);

        if ($code === '') {
            return '';
        }

        if (preg_match('/^dwy-/i', $code)) {
            return 'DWY-' . str_pad((string) (int) preg_replace('/\D/', '', substr($code, 4)), 4, '0', STR_PAD_LEFT);
        }

        return preg_replace('/\s+/', '', $code) ?? $code;
    }

    public function nextInternalCode(): string
    {
        $last = Item::query()
            ->where('barcode', 'like', 'DWY-%')
            ->orderByRaw('CAST(SUBSTRING(barcode, 5) AS UNSIGNED) DESC')
            ->value('barcode');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'DWY-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function findActiveByBarcode(string $code): ?Item
    {
        $normalized = $this->normalize($code);

        if ($normalized === '') {
            return null;
        }

        return Item::query()
            ->with('stock')
            ->where('active', true)
            ->where('sector', 'papeleria')
            ->where(function ($query) use ($normalized, $code) {
                $query->where('barcode', $normalized)
                    ->orWhere('barcode', trim($code));
            })
            ->first();
    }

    public function suggestedSalePrice(float $cost, float $markupPercent = 40): int
    {
        if ($cost <= 0) {
            return 0;
        }

        return (int) round($cost * (1 + ($markupPercent / 100)));
    }

    public function assertUniqueBarcode(?string $barcode, ?int $ignoreItemId = null): void
    {
        if ($barcode === null || $barcode === '') {
            return;
        }

        $normalized = $this->normalize($barcode);

        $exists = Item::query()
            ->when($ignoreItemId, fn ($q) => $q->where('id', '!=', $ignoreItemId))
            ->where('barcode', $normalized)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'barcode' => 'Ese código de barras ya está registrado.',
            ]);
        }
    }

    public function applyPapeleriaDefaults(array $data, bool $isCreate, ?Item $item = null): array
    {
        $sector = (string) ($data['sector'] ?? $item?->sector ?? '');

        if ($sector !== 'papeleria') {
            if ($isCreate || array_key_exists('sector', $data)) {
                $data['barcode'] = null;
                $data['scan_mode'] = null;
                $data['pack_size'] = null;
                $data['barcode_source'] = null;
                $data['internal_sku'] = null;
                $data['color'] = 'N/A';
            }

            return $data;
        }

        if (! $isCreate && ! array_intersect(array_keys($data), ['sector', 'type', 'barcode', 'color', 'scan_mode', 'pack_size', 'barcode_source', 'name'])) {
            return $data;
        }

        $data['color'] = trim((string) ($data['color'] ?? $item?->color ?? '')) ?: 'N/A';

        $type = (string) ($data['type'] ?? $item?->type ?? 'product');

        if ($type === 'product') {
            $data['scan_mode'] = in_array($data['scan_mode'] ?? $item?->scan_mode ?? 'unit', ['unit', 'pack'], true)
                ? ($data['scan_mode'] ?? $item?->scan_mode ?? 'unit')
                : 'unit';
        } else {
            if ($isCreate || array_key_exists('type', $data)) {
                $data['scan_mode'] = null;
                $data['pack_size'] = null;
            }
        }

        if (! empty($data['barcode'])) {
            $data['barcode'] = $this->normalize((string) $data['barcode']);
            if (preg_match('/^DWY-/i', $data['barcode'])) {
                $data['barcode_source'] = $data['barcode_source'] ?? 'internal';
                $data['internal_sku'] = $data['internal_sku'] ?? $data['barcode'];
            }
        } elseif ($isCreate && ($data['barcode_source'] ?? '') === 'internal') {
            $data['barcode'] = $this->nextInternalCode();
            $data['internal_sku'] = $data['barcode'];
        }

        return $data;
    }
}
