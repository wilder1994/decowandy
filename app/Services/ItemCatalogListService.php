<?php

namespace App\Services;

use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ItemCatalogListService
{
    public const MAX_EXPORT = 500;

    /**
     * @return Collection<int, array{id: int, name: string, barcode: ?string, color: ?string, sale_price: float}>
     */
    public function exportForSector(string $sector): Collection
    {
        return Item::query()
            ->where('sector', $sector)
            ->where('active', true)
            ->orderBy('name')
            ->limit(self::MAX_EXPORT)
            ->get(['id', 'name', 'barcode', 'color', 'sale_price'])
            ->map(fn (Item $item) => $this->mapListRow($item));
    }

    /**
     * @param  list<int>  $itemIds
     */
    public function renderPdf(string $sector, array $itemIds): Response
    {
        $items = Item::query()
            ->whereIn('id', $itemIds)
            ->where('sector', $sector)
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'barcode', 'color', 'sale_price']);

        if ($items->count() !== count($itemIds)) {
            throw ValidationException::withMessages([
                'item_ids' => 'Uno o más productos no pertenecen al sector seleccionado o no están activos.',
            ]);
        }

        $sectorLabel = (string) (config('decowandy.sectors')[$sector] ?? $sector);
        $rows = $items->map(fn (Item $item) => $this->mapListRow($item));

        $pdf = Pdf::loadView('items.catalog-list-pdf', [
            'sectorLabel' => $sectorLabel,
            'generatedAt' => now()->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            'rows' => $rows,
        ])->setPaper('letter', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="lista-productos-decowandy.pdf"',
        ]);
    }

    /**
     * @return array{id: int, name: string, barcode: ?string, color: ?string, sale_price: float}
     */
    private function mapListRow(Item $item): array
    {
        $color = trim((string) ($item->color ?? ''));
        if ($color === '' || strcasecmp($color, 'N/A') === 0) {
            $color = null;
        }

        $barcode = trim((string) ($item->barcode ?? ''));

        return [
            'id' => $item->id,
            'name' => $item->name,
            'barcode' => $barcode !== '' ? $barcode : null,
            'color' => $color,
            'sale_price' => (float) $item->sale_price,
        ];
    }
}
