<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchasePapeleriaService
{
    public function __construct(private readonly ItemBarcodeService $barcodes) {}

    public function isPapeleriaCategory(string $category): bool
    {
        $normalized = Str::lower(Str::ascii(trim($category)));

        return in_array($normalized, ['papeleria', 'papelería'], true);
    }

    /**
     * @param  array<string, mixed>  $line
     */
    public function resolveItemId(array $line, int $unitCost): int
    {
        if (! empty($line['item_id'])) {
            return (int) $line['item_id'];
        }

        $barcode = trim((string) ($line['barcode'] ?? ''));

        if ($barcode !== '') {
            $existing = $this->barcodes->findActiveByBarcode($barcode);

            if ($existing) {
                $this->refreshItemPricing($existing, $unitCost, $line);

                return $existing->id;
            }
        }

        return $this->createItemFromPurchaseLine($line, $unitCost, $barcode)->id;
    }

    /**
     * @param  array<string, mixed>  $line
     */
    private function createItemFromPurchaseLine(array $line, int $unitCost, string $barcode): Item
    {
        $name = trim((string) ($line['product_name'] ?? ''));

        if ($name === '') {
            throw ValidationException::withMessages([
                'items' => 'Cada línea de papelería debe tener nombre de producto.',
            ]);
        }

        $salePrice = isset($line['sale_price'])
            ? (float) $line['sale_price']
            : (float) $this->barcodes->suggestedSalePrice((float) $unitCost);

        $data = $this->barcodes->applyPapeleriaDefaults([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'sector' => 'papeleria',
            'type' => 'product',
            'sale_price' => $salePrice,
            'cost' => (float) $unitCost,
            'barcode' => $barcode !== '' ? $barcode : null,
            'barcode_source' => $barcode === '' ? 'internal' : (preg_match('/^DWY-/i', $barcode) ? 'internal' : 'manufacturer'),
            'color' => trim((string) ($line['color'] ?? '')) ?: 'N/A',
            'scan_mode' => $line['scan_mode'] ?? 'unit',
            'pack_size' => isset($line['pack_size']) ? (int) $line['pack_size'] : null,
            'active' => true,
            'featured' => false,
        ], true);

        if (! empty($data['barcode'])) {
            $this->barcodes->assertUniqueBarcode($data['barcode']);
        }

        $item = Item::create($data);

        Stock::firstOrCreate(
            ['item_id' => $item->id],
            ['quantity' => 0, 'min_threshold' => (int) ($line['min_stock'] ?? 0)]
        );

        return $item;
    }

    /**
     * @param  array<string, mixed>  $line
     */
    private function refreshItemPricing(Item $item, int $unitCost, array $line): void
    {
        $item->cost = (float) $unitCost;

        if (isset($line['sale_price']) && (float) $line['sale_price'] > 0) {
            $item->sale_price = (float) $line['sale_price'];
        }

        $item->save();
    }
}
