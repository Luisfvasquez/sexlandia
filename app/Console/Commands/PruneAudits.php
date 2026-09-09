<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneAudits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audits:prune {--months= : Meses de antigüedad a conservar (por defecto AUDIT_RETENTION_MONTHS o 6)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina registros de la tabla de auditoría (owen-it/laravel-auditing) más antiguos que N meses.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = (int) ($this->option('months') ?: env('AUDIT_RETENTION_MONTHS', 6));
        $months = max(1, $months);

        $cutoff = now()->subMonths($months);

        $deleted = DB::table('audits')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Auditorías eliminadas anteriores a {$cutoff->toDateTimeString()}: {$deleted}");

        return self::SUCCESS;
    }
}
