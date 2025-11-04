<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    // Página pública
    public function welcome()
    {
        // Traemos TODO el catálogo visible
        $base = DB::table('catalog_items')
            ->where('visible', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($x) {
                // normalizar imagen
                if ($x->image_path && !Str::startsWith($x->image_path, ['http://', 'https://', '/storage'])) {
                    $x->image_path = Storage::url($x->image_path);
                }
                return $x;
            });

        // Separamos por categoría
        $papeleria = $base->where('category', 'Papelería')->values();
        $impresion = $base->where('category', 'Impresión')->values();
        $diseno    = $base->where('category', 'Diseño')->values();

        // Destacados
        $destacados = $base->where('featured', 1)->take(6)->values();

        return view('welcome', compact('papeleria', 'impresion', 'diseno', 'destacados'));
    }

    // === API DEL EDITOR ===
    public function index(Request $request)
    {
        $q = DB::table('catalog_items');

        if ($request->boolean('featured')) {
            $q->where('featured', 1)->where('visible', 1);
        } else {
            if ($cat = $request->query('category')) {
                $q->where('category', $cat);
            }
        }

        $items = $q->orderBy('sort_order')->orderBy('id')->get()->map(function ($x) {
            $x->price = (int) ($x->price ?? 0);
            if ($x->image_path && !Str::startsWith($x->image_path, ['http://', 'https://', '/storage'])) {
                $x->image_path = Storage::url($x->image_path);
            }
            return $x;
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    // Crear
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        // sort_order al final de esa categoría
        $max = DB::table('catalog_items')
            ->where('category', $data['category'])
            ->max('sort_order');

        $data['sort_order'] = (int) $max + 1;

        $id = DB::table('catalog_items')->insertGetId($data);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        DB::table('catalog_items')->where('id', $id)->update($data);

        return response()->json(['ok' => true]);
    }

    // Eliminar definitivo (porque tu tabla no tiene deleted_at)
    public function destroy($id)
    {
        DB::table('catalog_items')->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    // Reordenar
    public function sort(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'ids'      => 'required|array',
        ]);

        foreach ($request->ids as $i => $id) {
            DB::table('catalog_items')
                ->where('id', $id)
                ->where('category', $request->category)
                ->update(['sort_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    private function validateData(Request $request): array
    {
        $v = $request->validate([
            'category'   => 'required|string|in:Papelería,Impresión,Diseño',
            'title'      => 'required|string|max:200',
            'description'=> 'nullable|string|max:1000',
            'price'      => 'nullable|integer|min:0',
            'show_price' => 'nullable|in:0,1',
            'visible'    => 'nullable|in:0,1',
            'featured'   => 'nullable|in:0,1',
        ]);

        $v['show_price'] = (int) ($v['show_price'] ?? 0);
        $v['visible']    = (int) ($v['visible'] ?? 1);
        $v['featured']   = (int) ($v['featured'] ?? 0);

        return $v;
    }
}
