<?php

namespace App\Services;

use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\Response;

class ItemLabelService
{
    public const MAX_SHEET_LABELS = 200;

    public const LABELS_PER_ROW = 5;

    public function __construct(private readonly ItemBarcodeService $barcodes) {}

    public function renderPng(Item $item): Response
    {
        $png = $this->buildCompactLabelPng($item);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="etiqueta-' . ($item->barcode ?? $item->id) . '.png"',
        ]);
    }

    /**
     * @param  list<array{item_id: int, quantity: int}>  $lines
     */
    public function renderSheetPdf(array $lines, bool $inline = false): Response
    {
        $labels = $this->expandLines($lines);

        $pdf = Pdf::loadView('items.labels.sheet-compact', [
            'labels' => $labels,
        ])->setPaper('letter', 'portrait');

        $disposition = $inline ? 'inline' : 'attachment';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="etiquetas-decowandy.pdf"',
        ]);
    }

    /**
     * @param  list<array{item_id: int, quantity: int}>  $lines
     * @return Collection<int, array{name: string, barcode: string, image: string}>
     */
    public function expandLines(array $lines): Collection
    {
        if ($lines === []) {
            throw ValidationException::withMessages([
                'lines' => 'Selecciona al menos un producto con código de barras.',
            ]);
        }

        $itemIds = collect($lines)->pluck('item_id')->unique()->values()->all();

        $items = Item::query()
            ->whereIn('id', $itemIds)
            ->where('sector', 'papeleria')
            ->whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->get()
            ->keyBy('id');

        $expanded = collect();
        $total = 0;
        $imageCache = [];

        foreach ($lines as $line) {
            $item = $items->get($line['item_id']);

            if ($item === null) {
                throw ValidationException::withMessages([
                    'lines' => 'Uno o más productos no son de papelería o no tienen código de barras.',
                ]);
            }

            $quantity = max(1, min(999, (int) $line['quantity']));
            $total += $quantity;

            if ($total > self::MAX_SHEET_LABELS) {
                throw ValidationException::withMessages([
                    'lines' => 'Máximo ' . self::MAX_SHEET_LABELS . ' etiquetas por hoja.',
                ]);
            }

            if (! isset($imageCache[$item->id])) {
                $imageCache[$item->id] = base64_encode($this->buildCompactLabelPng($item));
            }

            $label = [
                'name' => $item->name,
                'barcode' => (string) $item->barcode,
                'image' => $imageCache[$item->id],
            ];

            for ($i = 0; $i < $quantity; $i++) {
                $expanded->push($label);
            }
        }

        return $expanded;
    }

    public function buildCompactLabelPng(Item $item): string
    {
        $code = $item->barcode ?? '';

        if ($code === '') {
            throw new \InvalidArgumentException('El ítem no tiene código de barras.');
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($code, $generator::TYPE_CODE_128, 1, 24);

        $width = 148;
        $nameLines = $this->nameLines($item->name, 18, 2);
        $lineHeight = 9;
        $topPad = 2;
        $nameToBarcodeGap = 5;

        $barcodeImg = imagecreatefromstring($barcodePng);
        $destW = 0;
        $destH = 0;

        if ($barcodeImg !== false) {
            $bw = imagesx($barcodeImg);
            $bh = imagesy($barcodeImg);
            $maxW = $width - 8;
            $scale = min($maxW / max(1, $bw), 1.0);
            $destW = (int) round($bw * $scale);
            $destH = (int) round($bh * $scale);
        }

        $nameBlockHeight = count($nameLines) * $lineHeight;
        $barcodeY = $topPad + $nameBlockHeight + $nameToBarcodeGap;
        $codeY = $barcodeY + $destH + 3;
        $height = $codeY + 9;

        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $textColor = imagecolorallocate($canvas, 45, 42, 50);
        $muted = imagecolorallocate($canvas, 107, 101, 115);
        imagefill($canvas, 0, 0, $white);

        foreach ($nameLines as $index => $line) {
            $textY = $topPad + ($index * $lineHeight);
            imagestring($canvas, 2, 4, $textY, $line, $textColor);
        }

        if ($barcodeImg !== false) {
            $x = (int) floor(($width - $destW) / 2);
            imagecopyresampled($canvas, $barcodeImg, $x, $barcodeY, 0, 0, $destW, $destH, imagesx($barcodeImg), imagesy($barcodeImg));
            imagedestroy($barcodeImg);
        }

        $codeX = max(2, (int) floor(($width - (strlen($code) * 6)) / 2));
        imagestring($canvas, 1, $codeX, $codeY, $code, $muted);

        ob_start();
        imagepng($canvas);
        $png = (string) ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }

    /**
     * @return list<string>
     */
    private function nameLines(string $name, int $maxChars, int $maxLines): array
    {
        $name = trim($name);

        if ($name === '') {
            return [''];
        }

        if (mb_strlen($name) <= $maxChars) {
            return [$name];
        }

        $first = mb_substr($name, 0, $maxChars);
        $rest = mb_substr($name, $maxChars);

        if ($maxLines < 2 || mb_strlen($rest) === 0) {
            return [rtrim($first) . '…'];
        }

        $second = mb_strlen($rest) > $maxChars
            ? mb_substr($rest, 0, $maxChars - 1) . '…'
            : $rest;

        return [$first, $second];
    }

    /** @deprecated Use buildCompactLabelPng() — kept for backward compatibility */
    public function buildLabelPng(Item $item): string
    {
        return $this->buildCompactLabelPng($item);
    }

    private function truncate(string $value, int $max): string
    {
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max - 1) . '…';
    }
}
