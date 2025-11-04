<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Reference;
use App\Models\Session;
use App\Models\Training;
use App\Services\AtomicCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferenceGeneratorService
{
    /**
     * Génère (ou retourne) la référence pour un enrollment (idempotent).
     *
     * Format : CC_DOCKIND_TRAININGCODE_MMYYYY_PILINC_GLOBINC
     * Ex     : CI_CER_INNOV-007-F_112025_0001_00042
     *
     * Règle de comptage :
     *  - pillar_increment = total CER/ATT du pilier (toute l'histoire)
     *  - global_increment = total CER/ATT global (toute l'histoire)
     */
    public function generateForEnrollment(Enrollment $enrollment, ?string $countryCode = 'CI'): Reference
    {
        // 1) Idempotence
        if ($enrollment->reference()->exists()) {
            return $enrollment->reference;
        }

        // 2) Pré-chargement des relations
        $enrollment->loadMissing(['session.training.pillar', 'participant']);

        $session     = $enrollment->session;
        $training    = $session->training ?? null;
        $pillar      = $training?->pillar;
        $participant = $enrollment->participant;

        // Garde : données indispensables
        if (!$training || !$pillar) {
            throw new \RuntimeException('Training ou Pillar manquant pour la session.');
        }

        // 3) Date & période d’affichage
        $dt        = Carbon::parse($session->end_date ?? now());
        $monthYear = $dt->format('mY'); // ex '112025'

        // 4) Type de document (convention actuelle : CER / ATT)
        $docKind = $training->has_exam ? 'CER' : 'ATT';

        // 5) Code formation : custom si présent
        $trainingCode = ($training->is_custom && $training->custom_code)
            ? $training->custom_code
            : $training->code;

        // 6) Compteurs atomiques (SANS scoper par mois)
        $globalInc = AtomicCounter::next('GLOBAL');                        // total global
        $pillarInc = AtomicCounter::next('PILLAR:' . $pillar->id);         // total du pilier

        // 7) Construction de la référence
        $referenceString = $this->formatReference(
            countryCode: strtoupper($countryCode ?? 'CI'),
            docKind: $docKind,
            trainingCode: $trainingCode,
            monthYear: $monthYear,
            pillarIncrement: $pillarInc,
            globalIncrement: $globalInc
        );

        // 8) Création de l'enregistrement
        return Reference::create([
            'uuid'             => (string) Str::uuid(), // si ta table a la colonne uuid
            'doc_kind'         => $docKind,
            'reference'        => $referenceString,
            'month_year'       => $monthYear,
            'global_increment' => $globalInc,
            'pillar_increment' => $pillarInc,
            'pillar_id'        => $pillar->id,
            'enrollment_id'    => $enrollment->id,
            'meta'             => $this->buildMeta(
                $countryCode,
                $trainingCode,
                $training->title,
                $pillar->name,
                $participant?->full_name,
                $participant?->id,
                $session->id,
                $dt
            ),
        ]);
    }

    /**
     * Génère des références pour TOUTES les inscriptions d'une session.
     * (Ne régénère pas celles déjà existantes.)
     *
     * @return array<Reference>
     */
    public function generateForSession(Session $session, ?string $countryCode = 'CI'): array
    {
        $session->loadMissing(['enrollments.participant', 'training.pillar']);

        $created = [];
        foreach ($session->enrollments as $enrollment) {
            if ($enrollment->reference()->exists()) {
                continue;
            }
            $created[] = $this->generateForEnrollment($enrollment, $countryCode);
        }
        return $created;
    }

    /**
     * Génère des références pour une liste d'enrollments.
     *
     * @param  array<int> $enrollmentIds
     * @return array<Reference>
     */
    public function generateForEnrollments(array $enrollmentIds, ?string $countryCode = 'CI'): array
    {
        $enrollments = Enrollment::with(['session.training.pillar', 'participant'])
            ->whereIn('id', $enrollmentIds)
            ->get();

        $created = [];
        foreach ($enrollments as $enrollment) {
            if ($enrollment->reference()->exists()) {
                continue;
            }
            $created[] = $this->generateForEnrollment($enrollment, $countryCode);
        }
        return $created;
    }

    /**
     * Vérifie l’existence d’une référence (pas d’alias ici).
     */
    public function verifyReference(string $referenceNumber): ?Reference
    {
        return Reference::where('reference', strtoupper($referenceNumber))
            ->with(['enrollment.participant', 'enrollment.session.training.pillar'])
            ->first();
    }

    /**
     * Export tableau 2D (prêt pour CSV/Excel).
     *
     * @param  array<int> $referenceIds
     * @return array<int, array<int, string|null>>
     */
    public function exportToArray(array $referenceIds = []): array
    {
        $query = Reference::with([
            'enrollment.participant',
            'enrollment.session.training.pillar',
        ])->orderBy('created_at', 'desc');

        if (!empty($referenceIds)) {
            $query->whereIn('id', $referenceIds);
        }

        $rows = $query->get();

        $data   = [];
        $data[] = [
            'Numéro de Référence',
            'Type de Document',
            'Nom du Participant',
            'Email',
            'Téléphone',
            'Entreprise',
            'Formation',
            'Pilier',
            'Date de Session',
            'Date de Génération',
        ];

        foreach ($rows as $ref) {
            $enrollment  = $ref->enrollment;
            $participant = $enrollment?->participant;
            $session     = $enrollment?->session;
            $training    = $session?->training;
            $pillar      = $training?->pillar;

            $sessionDate = $session?->end_date
                ? Carbon::parse($session->end_date)->format('d/m/Y')
                : null;

            $data[] = [
                $ref->reference,
                $ref->doc_kind === 'CER' ? 'Certificat' : 'Attestation',
                $participant?->full_name,
                $participant?->email,
                $participant?->phone,
                $participant?->company,
                $training?->title,
                $pillar?->name,
                $sessionDate,
                $ref->created_at->format('d/m/Y H:i'),
            ];
        }

        return $data;
    }

    /**
     * Statistiques rapides (basées sur colonnes indexées).
     */
    public function getStatistics(): array
    {
        $total     = Reference::count();
        $totalCer  = Reference::where('doc_kind', 'CER')->count();
        $totalAtt  = Reference::where('doc_kind', 'ATT')->count();

        $byPillar = DB::table('references')
            ->join('pillars', 'references.pillar_id', '=', 'pillars.id')
            ->select('pillars.name', DB::raw('COUNT(*) AS count'))
            ->groupBy('pillars.id', 'pillars.name')
            ->orderByDesc('count')
            ->get();

        $byMonth = DB::table('references')
            ->select('month_year', DB::raw('COUNT(*) AS count'))
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get();

        return [
            'total_references'   => $total,
            'total_certificates' => $totalCer,
            'total_attestations' => $totalAtt,
            'by_pillar'          => $byPillar,
            'by_month'           => $byMonth,
        ];
    }

    /**
     * Builder du numéro (facile à changer si la règle évolue).
     */
    protected function formatReference(
        string $countryCode,
        string $docKind,
        string $trainingCode,
        string $monthYear,
        int $pillarIncrement,
        int $globalIncrement
    ): string {
        return sprintf(
            '%s_%s_%s_%s_%04d_%05d',
            strtoupper($countryCode),
            $docKind,
            $trainingCode,
            $monthYear,
            $pillarIncrement,
            $globalIncrement
        );
    }

    /**
     * Métadonnées de traçabilité.
     */
    protected function buildMeta(
        ?string $countryCode,
        string $trainingCode,
        ?string $trainingTitle,
        ?string $pillarName,
        ?string $participantName,
        ?int $participantId,
        int $sessionId,
        Carbon $sessionDate
    ): array {
        return [
            'country_code'     => strtoupper($countryCode ?? 'CI'),
            'training_code'    => $trainingCode,
            'training_title'   => $trainingTitle,
            'pillar_name'      => $pillarName,
            'participant_name' => $participantName,
            'participant_id'   => $participantId,
            'session_id'       => $sessionId,
            'session_date'     => $sessionDate->format('Y-m-d'),
        ];
    }
}
