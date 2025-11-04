<?php
// app/Services/AtomicCounter.php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class AtomicCounter
{
    /**
     * Retourne la prochaine valeur (1,2,3,...) pour une clé de scope donnée.
     * Implémente un upsert atomique MySQL/InnoDB.
     */
    public static function next(string $scopeKey): int
    {
        DB::statement("
            INSERT INTO reference_counters (scope_key, counter, updated_at, created_at)
            VALUES (?, 0, NOW(), NOW())
            ON DUPLICATE KEY UPDATE counter = LAST_INSERT_ID(counter + 1), updated_at = NOW()
        ", [$scopeKey]);

        $row = DB::selectOne("SELECT LAST_INSERT_ID() AS c");
        return (int) $row->c;
    }

    /**
     * Réserve un bloc (optionnel) — utile pour générer en masse plus vite.
     * Retourne [start+1, ..., start+size].
     */
    public static function reserve(string $scopeKey, int $size): array
    {
        DB::statement("
            INSERT INTO reference_counters (scope_key, counter, updated_at, created_at)
            VALUES (?, 0, NOW(), NOW())
            ON DUPLICATE KEY UPDATE counter = LAST_INSERT_ID(counter + ?), updated_at = NOW()
        ", [$scopeKey, $size]);

        $end = (int) DB::selectOne("SELECT LAST_INSERT_ID() AS c")->c;
        $start = $end - $size;
        // Génère la séquence (start+1 ... end)
        return range($start + 1, $end);
    }
}
