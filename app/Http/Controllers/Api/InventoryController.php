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
        $rows = DB::table('stocks as s')
            ->join('items as i', 'i.id', '=', 's.item_id')
            ->select('i.id as item_id', 'i.name', 'i.category', 's.quantity', 's.min_threshold')
            ->where(function ($q) {
                $q->where('s.quantity', '<=', DB::raw('COALESCE(s.min_threshold, 0)'))
                    ->orWhere('s.quantity', '<=', 0);
            })
            ->orderBy('s.quantity')
            ->orderBy('i.name')
            ->get();


        return response()->json($rows);
    }
}
