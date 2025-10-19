<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::orderByDesc('id')->paginate(10);
        return view('sales.index', compact('sales'));
    }

    /**
     * Formulario para registrar una venta (POS)
     */
    public function create(Request $request)
    {
        // Sector recibido desde la ruta (diseno / impresion / papeleria)
        $sector = $request->get('sector', 'papeleria');

        // Filtrar ítems por sector activo
        $items = Item::where('sector', $sector)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('items', 'sector'));
    }


    /**
     * Guarda la venta + detalle e imprime PDF
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'   => 'nullable|string|max:100',
            'customer_email'  => 'nullable|email|max:100',
            'customer_phone'  => 'nullable|string|max:30',
            'amount_received' => 'required|numeric|min:0',
            'items'           => 'required|array|min:1',
            'items.*.id'      => 'required|exists:items,id',
            'items.*.quantity'=> 'required|numeric|min:1',
            // Si quieres aceptar precio desde el cliente, al menos valida:
            'items.*.price'   => 'required|numeric|min:0',
            'payment_method'  => 'nullable|in:cash,transfer,card,mixed,other',
        ]);

        DB::beginTransaction();
        try {
            // 1) Traer items desde BD y recalcular totales
            $itemIds = collect($request->items)->pluck('id')->all();
            $itemsDb = Item::whereIn('id', $itemIds)->get()->keyBy('id');

            $lines = [];
            $subtotal = 0;

            foreach ($request->items as $row) {
                $it = $itemsDb[$row['id']] ?? null;
                if (!$it) {
                    throw new \Exception("Producto {$row['id']} no encontrado.");
                }

                $qty = (float) $row['quantity'];

                // Precio de venta confiable:
                $unitPrice = (float) ($row['price'] ?? $it->sale_price); // o fuerza $it->sale_price

                $lineTotal = round($qty * $unitPrice, 2);
                $subtotal += $lineTotal;

                // Verificar stock (opcional: solo si manejas stock estricto)
                if (!is_null($it->stock) && $it->stock < $qty) {
                    throw new \Exception("Stock insuficiente para {$it->name}.");
                }

                $lines[] = [
                    'item'        => $it,
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'line_total'  => $lineTotal,
                ];
            }

            $discount = 0; // ajustar si luego aplicas descuentos
            $taxes    = 0; // ajustar si luego aplicas impuestos
            $total    = round($subtotal - $discount + $taxes, 2);

            // 2) Validar recibido vs total
            $amountReceived = (float) $request->amount_received;
            $change = $amountReceived >= $total ? round($amountReceived - $total, 2) : 0;

            // 3) Crear venta
            $sale = Sale::create([
                'sale_code'       => 'DW-' . date('YmdHis'),
                'date'            => now()->toDateString(),
                'time'            => now()->toTimeString(),
                'user_id'         => Auth::id(),
                'sector'          => $request->sector ?? 'papeleria',
                'customer_name'   => $request->customer_name,
                'customer_email'  => $request->customer_email,
                'customer_phone'  => $request->customer_phone,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'taxes'           => $taxes,
                'total'           => $total,
                'amount_received' => $amountReceived,
                'change_due'      => $change,
                'payment_method'  => $request->payment_method ?? 'cash',
            ]);


            // 4) Crear líneas y actualizar inventario
            foreach ($lines as $L) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'item_id'    => $L['item']->id,
                    'quantity'   => $L['quantity'],
                    'unit_price' => $L['unit_price'],
                    'line_total' => $L['line_total'],
                ]);

                // ↓ Si gestionas stock:
                if (!is_null($L['item']->stock)) {
                    $L['item']->decrement('stock', $L['quantity']);
                }

                // ↓ Registrar movimiento (opcional pero recomendado)
                \App\Models\StockMovement::create([
                    'item_id'   => $L['item']->id,
                    'type'      => 'out', // salida por venta
                    'quantity'  => $L['quantity'],
                    'note'      => 'Venta ID '.$sale->id,
                    'date'      => now()->toDateString(),
                ]);
            }

            // 5) Cargar relaciones para PDF
            $sale->load(['items.item','user']);

            // 6) Asegurar carpeta y generar PDF
            Storage::disk('public')->makeDirectory('invoices');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.invoice', ['sale' => $sale]);

            $fileName = 'invoice_' . $sale->id . '.pdf';
            Storage::disk('public')->put('invoices/'.$fileName, $pdf->output());

            // 7) Guardar ruta PDF en venta
            $sale->update(['invoice_pdf_path' => 'invoices/'.$fileName]);

            DB::commit();

            return redirect()
                ->route('dashboard')
                ->with('success', 'Venta registrada y factura generada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }


     public function invoice(Sale $sale)
    {
        if ($sale->invoice_pdf_path) {
            return redirect()->to(asset('storage/'.$sale->invoice_pdf_path));
        }

        // Fallback: renderizar la vista de factura (HTML) para imprimir/guardar como PDF
        // Si usas DOMPDF, aquí podrías generar el PDF en caliente y retornarlo como stream.
        return view('sales.invoice', compact('sale'));
    }
}
