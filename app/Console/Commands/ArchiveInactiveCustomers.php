<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveInactiveCustomers extends Command
{
    protected $signature = 'customers:archive-inactive {--months=6 : Meses sin compras para archivar}';

    protected $description = 'Archiva clientes sin compras recientes y devuelve el total procesado.';

    public function handle(): int
    {
        $months = (int) $this->option('months');
        $cutoff = Carbon::now()->subMonths($months);

        $updated = Customer::query()
            ->whereNull('archived_at')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_purchase_at')
                  ->orWhere('last_purchase_at', '<', $cutoff);
            })
            ->update(['archived_at' => Carbon::now()]);

        $this->info("Clientes archivados: {$updated}");

        return Command::SUCCESS;
    }
}
