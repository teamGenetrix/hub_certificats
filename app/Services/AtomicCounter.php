<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class AtomicCounter
{
    public static function next(string $scopeKey): int
    {
        // Étape 1: s'assurer que la ligne existe (sans incrément)
        DB::statement("
            INSERT INTO reference_counters (scope_key, counter, created_at, updated_at)
            VALUES (?, 0, NOW(), NOW())
            ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at)
        ", [$scopeKey]);

        // Étape 2: incrément atomique + retour de la valeur incrémentée
        DB::statement("
            UPDATE reference_counters
            SET counter = LAST_INSERT_ID(counter + 1), updated_at = NOW()
            WHERE scope_key = ?
        ", [$scopeKey]);

        $row = DB::selectOne("SELECT LAST_INSERT_ID() AS c");
        return (int) $row->c;
    }

    // Optionnel: reset d'un scope pour repartir à 0
    public static function reset(string $scopeKey): void
    {
        DB::table('reference_counters')->where('scope_key', $scopeKey)->delete();
    }
}
