<?php

namespace Tests\Unit;

use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resumen_and_ingresos_vs_gastos(): void
    {
        $service = new FinanceService();

        $sales = collect([(object) ['total' => 150000]]);
        $expenses = collect([(object) ['amount' => 20000]]);
        $purchases = collect([(object) ['total' => 30000]]);
        $investments = collect([(object) ['amount' => 50000]]);

        $resumen = $service->resumen($sales, $expenses, $purchases, $investments);

        $this->assertSame(150000, $resumen['ingresos']);
        $this->assertSame(50000, $resumen['egresos']);
        $this->assertSame(50000, $resumen['invertido']);
        $this->assertSame(100000, $resumen['utilidad']);
        $this->assertSame(50000, $resumen['caja']);

        $dataset = $service->ingresosVsGastosDataset($resumen['ingresos'], $resumen['egresos'], $resumen['invertido']);
        $this->assertSame([150000, 50000, 50000], $dataset['data']);
    }

    public function test_cashflow_dataset_groups_by_day(): void
    {
        $service = new FinanceService();
        $from = Carbon::parse('2025-02-01');
        $to = Carbon::parse('2025-02-03');

        $sales = collect([
            new \App\Models\Sale(['sold_at' => Carbon::parse('2025-02-01 10:00:00'), 'total' => 10000]),
            new \App\Models\Sale(['sold_at' => Carbon::parse('2025-02-03 09:00:00'), 'total' => 20000]),
        ]);

        $expenses = collect([(object) ['date' => '2025-02-02', 'amount' => 5000]]);
        $purchases = collect([(object) ['date' => '2025-02-03', 'total' => 3000]]);
        $investments = collect();

        $dataset = $service->cashflowDataset($sales, $expenses, $purchases, $investments, $from, $to);

        $this->assertSame(['01/02', '02/02', '03/02'], $dataset['labels']);
        $this->assertSame([10000, 0, 20000], $dataset['entradas']);
        $this->assertSame([0, 5000, 3000], $dataset['salidas']);
    }
}
