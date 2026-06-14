<?php

namespace App\Services;

use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\Response;

class ItemLabelService
{
    public function __construct(private readonly ItemBarcodeService $barcodes) {}

    public function renderPng(Item $item): Response
    {
        $png = $this->buildLabelPng($item);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="etiqueta-' . ($item->barcode ?? $item->id) . '.png"',
        ]);
    }

    public function renderSheetPdf(array $itemIds): Response
    {
        $items = Item::query()
            ->whereIn('id', $itemIds)
            ->where('sector', 'papeleria')
            ->whereNotNull('barcode')
            ->orderBy('name')
            ->get();

        $labels = $items->map(function (Item $item) {
            return [
                'name' => $item->name,
                'color' => $item->color !== 'N/A' ? $item->color : null,
                'barcode' => $item->barcode,
                'price' => (int) round($item->sale_price),
                'image' => base64_encode($this->buildLabelPng($item)),
            ];
        });

        $pdf = Pdf::loadView('items.labels.sheet', [
            'labels' => $labels,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('etiquetas-decowandy.pdf');
    }

    public function buildLabelPng(Item $item): string
    {
        $code = $item->barcode ?? '';

        if ($code === '') {
            throw new \InvalidArgumentException('El ítem no tiene código de barras.');
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 42);

        $qrOptions = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 3,
            'margin' => 0,
        ]);
        $qrPng = (new QRCode($qrOptions))->render($code);

        $width = 360;
        $height = 200;
        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $textColor = imagecolorallocate($canvas, 45, 42, 50);
        $muted = imagecolorallocate($canvas, 107, 101, 115);
        imagefill($canvas, 0, 0, $white);

        $name = $this->truncate($item->name, 28);
        imagestring($canvas, 4, 12, 10, $name, $textColor);

        $meta = $item->color !== 'N/A' ? $item->color : 'Papelería';
        imagestring($canvas, 3, 12, 32, $meta, $muted);
        imagestring($canvas, 3, 12, 52, '$' . number_format((int) round($item->sale_price), 0, ',', '.'), $textColor);

        $barcodeImg = imagecreatefromstring($barcodePng);
        if ($barcodeImg !== false) {
            imagecopy($canvas, $barcodeImg, 12, 78, 0, 0, imagesx($barcodeImg), imagesy($barcodeImg));
            imagedestroy($barcodeImg);
        }

        $qrImg = imagecreatefromstring($qrPng);
        if ($qrImg !== false) {
            $qrSize = 72;
            imagecopyresampled($canvas, $qrImg, $width - $qrSize - 12, 78, 0, 0, $qrSize, $qrSize, imagesx($qrImg), imagesy($qrImg));
            imagedestroy($qrImg);
        }

        imagestring($canvas, 2, 12, 168, $code, $muted);

        ob_start();
        imagepng($canvas);
        $png = (string) ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }

    private function truncate(string $value, int $max): string
    {
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max - 1) . '…';
    }
}
