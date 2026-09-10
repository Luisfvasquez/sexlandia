<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Inserta una fila mínima en la tabla de auditoría con la fecha indicada.
 */
function seedAuditRow(CarbonInterface $createdAt): void
{
    DB::table('audits')->insert([
        'event' => 'updated',
        'auditable_type' => 'App\\Models\\Product',
        'auditable_id' => 1,
        'old_values' => '{}',
        'new_values' => '{}',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}

test('audits:prune deletes only audit rows older than the retention window', function () {
    seedAuditRow(now()->subMonths(8));
    seedAuditRow(now()->subDays(3));

    expect(DB::table('audits')->count())->toBe(2);

    $this->artisan('audits:prune')->assertSuccessful();

    expect(DB::table('audits')->count())->toBe(1);
    expect(DB::table('audits')->where('created_at', '<', now()->subMonths(6))->count())->toBe(0);
});

test('audits:prune honours the --months option', function () {
    seedAuditRow(now()->subMonths(2));
    seedAuditRow(now()->subDays(1));

    $this->artisan('audits:prune', ['--months' => 1])->assertSuccessful();

    expect(DB::table('audits')->count())->toBe(1);
});
