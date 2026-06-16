<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 8mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #2d2a32; margin: 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { width: 20%; padding: 1px; vertical-align: top; }
        .label {
            border: 0.5px solid #d8d0e8;
            border-radius: 2px;
            padding: 1px;
            box-sizing: border-box;
            overflow: hidden;
            text-align: center;
            line-height: 0;
        }
        .img { display: block; width: 100%; height: auto; margin: 0 auto; }
    </style>
</head>
<body>
    <table>
        @foreach($labels->chunk(\App\Services\ItemLabelService::LABELS_PER_ROW) as $row)
            <tr>
                @foreach($row as $label)
                    <td>
                        <div class="label">
                            <img class="img" src="data:image/png;base64,{{ $label['image'] }}" alt="{{ $label['barcode'] }}">
                        </div>
                    </td>
                @endforeach
                @for($i = $row->count(); $i < \App\Services\ItemLabelService::LABELS_PER_ROW; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>
