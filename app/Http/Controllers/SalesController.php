<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $sectorLabels = [
            'diseno' => 'Diseño',
            'impresion' => 'Impresión',
            'papeleria' => 'Papelería',
        ];

        $category = $request->query('category', 'all');
        if ($category !== 'all' && !array_key_exists($category, $sectorLabels)) {
            $category = 'all';
        }

        $dateType = $request->query('date_type', 'month');
        if (!in_array($dateType, ['day', 'week', 'month'], true)) {
            $dateType = 'month';
        }

        [$range, $filterValues] = $this->resolveDateRange($dateType, $request);

        $salesQuery = Sale::with(['items.item'])
            ->orderByDesc('sold_at')
            ->orderByDesc('id');

        if ($range['start'] && $range['end']) {
            $salesQuery->whereBetween('sold_at', [$range['start'], $range['end']]);
        }

        $sales = $salesQuery->get();

        $sections = collect($sectorLabels)->map(fn ($label) => [
            'label' => $label,
            'rows' => [],
            'total' => 0,
        ])->all();

        $overallTotal = 0;

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $sector = $item->category ?? optional($item->item)->sector;

                if (!$sector || !isset($sections[$sector])) {
                    continue;
                }

                if ($category !== 'all' && $sector !== $category) {
                    continue;
                }

                $rowKey = $sale->id;

                if (!isset($sections[$sector]['rows'][$rowKey])) {
                    $sections[$sector]['rows'][$rowKey] = [
                        'sale' => $sale,
                        'items' => collect(),
                        'quantity' => 0,
                        'total' => 0,
                    ];
                }

                $sections[$sector]['rows'][$rowKey]['items']->push($item);
                $sections[$sector]['rows'][$rowKey]['quantity'] += (float) $item->quantity;
                $sections[$sector]['rows'][$rowKey]['total'] += (int) $item->line_total;

                $sections[$sector]['total'] += (int) $item->line_total;
                $overallTotal += (int) $item->line_total;
            }
        }

        foreach ($sections as $key => $section) {
            $sections[$key]['rows'] = collect($section['rows'])
                ->sortByDesc(function ($row) {
                    $date = $row['sale']->sold_at ?? $row['sale']->created_at;

                    if ($date instanceof Carbon) {
                        return $date->timestamp;
                    }

                    return $date ? Carbon::parse($date)->timestamp : 0;
                })
                ->map(function ($row) {
                    /** @var Collection $items */
                    $items = $row['items'];

                    return [
                        'sale' => $row['sale'],
                        'item_names' => $items->map(fn ($item) => $item->item->name ?? $item->description ?? 'Ítem #' . $item->item_id)->all(),
                        'quantity' => (float) $items->sum('quantity'),
                        'total' => $row['total'],
                    ];
                })
                ->values()
                ->all();
        }

        if ($category !== 'all') {
            $sections = Arr::only($sections, [$category]);
        }

                // Catálogo de ítems para el modal (datos reales)
        $catalogItems = Item::select('id', 'name', 'sale_price', 'sector')
            ->orderBy('name')
            ->get();

        // Lo convertimos en un dataset por sector para el JS del modal
        $catalogDataset = $catalogItems->groupBy('sector')->map(function ($group) {
            return $group->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->name,
                    'unit' => (int) $item->sale_price,
                ];
            })->values();
        });

                return view('sales.index', [
            'sections'      => $sections,
            'overallTotal'  => $overallTotal,
            'filters'       => [
                'category'   => $category,
                'date_type'  => $dateType,
                'day'        => $filterValues['day'],
                'week'       => $filterValues['week'],
                'month'      => $filterValues['month'],
                'range'      => $range,
            ],
            'sectorLabels'  => $sectorLabels,
            'catalogDataset'=> $catalogDataset,
        ]);
    }

    private function resolveDateRange(string $dateType, Request $request): array
    {
        $now = now();
        $range = [
            'start' => null,
            'end' => null,
            'label' => null,
        ];

        $values = [
            'day' => null,
            'week' => null,
            'month' => null,
        ];

        if ($dateType === 'day') {
            $day = $request->query('day');
            $date = $this->parseDate($day, 'Y-m-d') ?? $now;
            $range['start'] = $date->copy()->startOfDay();
            $range['end'] = $date->copy()->endOfDay();
            $range['label'] = $date->isoFormat('LL');
            $values['day'] = $date->format('Y-m-d');
        } elseif ($dateType === 'week') {
            $week = $request->query('week');
            $date = $this->parseIsoWeek($week) ?? $now->copy()->startOfWeek();
            $range['start'] = $date->copy()->startOfWeek();
            $range['end'] = $date->copy()->endOfWeek();
            $range['label'] = sprintf('Semana %s (%s - %s)',
                $range['start']->isoWeek,
                $range['start']->isoFormat('LL'),
                $range['end']->isoFormat('LL')
            );
            $values['week'] = $range['start']->format('o-\WW');
        } else {
            $month = $request->query('month');
            $date = $this->parseDate($month ? $month . '-01' : null, 'Y-m-d') ?? $now->copy()->startOfMonth();
            $range['start'] = $date->copy()->startOfMonth();
            $range['end'] = $date->copy()->endOfMonth();
            $range['label'] = $range['start']->isoFormat('MMMM YYYY');
            $values['month'] = $range['start']->format('Y-m');
        }

        // Asegura que los filtros restantes tengan valores por defecto coherentes.
        if (!$values['day']) {
            $values['day'] = $range['start']->format('Y-m-d');
        }
        if (!$values['week']) {
            $values['week'] = $range['start']->format('o-\WW');
        }
        if (!$values['month']) {
            $values['month'] = $range['start']->format('Y-m');
        }

        return [$range, $values];
    }

    private function parseDate(?string $value, string $format): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat($format, $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseIsoWeek(?string $value): ?Carbon
    {
        if (!$value || !preg_match('/^(\d{4})-W(\d{2})$/', $value, $matches)) {
            return null;
        }

        try {
            return Carbon::now()->setISODate((int) $matches[1], (int) $matches[2])->startOfWeek();
        } catch (\Throwable) {
            return null;
        }
    }
}
