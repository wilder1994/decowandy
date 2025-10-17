<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Papelería (product)
            ['name'=>'Papel bond carta (500h)','type'=>'product','sector'=>'papeleria','sale_price'=>25000,'cost'=>18000,'stock'=>20,'min_stock'=>5,'unit'=>'paquete'],
            ['name'=>'Carpeta oficio','type'=>'product','sector'=>'papeleria','sale_price'=>1200,'cost'=>700,'stock'=>100,'min_stock'=>20,'unit'=>'unidad'],

            // Impresión (service)
            ['name'=>'Copia B/N','type'=>'service','sector'=>'impresion','sale_price'=>200,'cost'=>50,'stock'=>0,'min_stock'=>0,'unit'=>'hoja'],
            ['name'=>'Impresión color','type'=>'service','sector'=>'impresion','sale_price'=>600,'cost'=>250,'stock'=>0,'min_stock'=>0,'unit'=>'hoja'],

            // Diseño (service)
            ['name'=>'Logo básico','type'=>'service','sector'=>'diseno','sale_price'=>150000,'cost'=>0,'stock'=>0,'min_stock'=>0,'unit'=>'servicio'],
            ['name'=>'Tarjetas personales (100u)','type'=>'service','sector'=>'diseno','sale_price'=>70000,'cost'=>30000,'stock'=>0,'min_stock'=>0,'unit'=>'paquete'],
        ];

        foreach ($items as $i) {
            Item::updateOrCreate(
                ['name' => $i['name']],
                [
                    'slug' => Str::slug($i['name']).'-'.Str::random(4),
                    'description' => null,
                    'type' => $i['type'],
                    'sector' => $i['sector'],
                    'sale_price' => $i['sale_price'],
                    'cost' => $i['cost'],
                    'stock' => $i['stock'],
                    'min_stock' => $i['min_stock'],
                    'unit' => $i['unit'],
                    'featured' => false,
                    'active' => true,
                ]
            );
        }
    }
}
