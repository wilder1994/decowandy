<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class InventoryController extends Controller
{
    /**
     * GET /api/stocks/low
     * Lista items con stock en 0 o por debajo del umbral.
     */
    public function lowStock()
    {
        $thresholdSql = 'COALESCE(s.min_threshold, i.min_stock, 0)';

        $rows = DB::table('stocks as s')
            ->join('items as i', 'i.id', '=', 's.item_id')
            ->select(
                'i.id as item_id',
                'i.name',
                'i.sector',
                's.quantity',
                DB::raw($thresholdSql . ' as min_threshold')
            )
            ->where(function ($q) use ($thresholdSql) {
                $q->where('s.quantity', '<=', DB::raw($thresholdSql))
                    ->orWhere('s.quantity', '<=', 0);
            })
            ->orderBy('s.quantity')
            ->orderBy('i.name')
            ->get();


        return response()->json($rows);
    }
}
