<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #2d2a32; }
        table { width: 100%; border-collapse: collapse; }
        td { width: 33.33%; padding: 6px; vertical-align: top; }
        .label {
            border: 1px solid #d8d0e8;
            border-radius: 6px;
            padding: 6px;
            height: 108px;
            box-sizing: border-box;
        }
        .name { font-size: 10px; font-weight: bold; margin-bottom: 2px; }
        .meta { font-size: 8px; color: #6b6573; margin-bottom: 4px; }
        .img { width: 100%; height: auto; }
    </style>
</head>
<body>
    <table>
        @foreach($labels->chunk(3) as $row)
            <tr>
                @foreach($row as $label)
                    <td>
                        <div class="label">
                            <div class="name">{{ $label['name'] }}</div>
                            <div class="meta">
                                @if($label['color']){{ $label['color'] }} · @endif
                                ${{ number_format($label['price'], 0, ',', '.') }}
                            </div>
                            <img class="img" src="data:image/png;base64,{{ $label['image'] }}" alt="{{ $label['barcode'] }}">
                        </div>
                    </td>
                @endforeach
                @for($i = $row->count(); $i < 3; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>
