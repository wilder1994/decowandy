<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #2d2a32; margin: 0; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { font-size: 9px; color: #6b6574; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d8d0e8; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f0f8; font-size: 9px; text-transform: uppercase; letter-spacing: 0.03em; }
        td.price { text-align: right; white-space: nowrap; }
        td.code { font-family: DejaVu Sans Mono, monospace; font-size: 9px; }
    </style>
</head>
<body>
    <h1>DecoWandy — Lista de productos</h1>
    <p class="meta">{{ $sectorLabel }} · {{ $generatedAt }} · {{ $rows->count() }} producto(s)</p>
    <table>
        <thead>
            <tr>
                <th style="width: 38%;">Nombre</th>
                <th style="width: 22%;">Código</th>
                <th style="width: 18%;">Color</th>
                <th style="width: 22%;">Precio (COP)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td class="code">{{ $row['barcode'] ?? '—' }}</td>
                    <td>{{ $row['color'] ?? '—' }}</td>
                    <td class="price">${{ number_format($row['sale_price'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
