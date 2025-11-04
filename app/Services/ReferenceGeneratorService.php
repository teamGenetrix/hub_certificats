<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Participant;
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
     * Format proposé : CC_DOCKIND_TRAININGCODE_MMYYYY_PILINC_GLOBINC
     * Ex: CI_CER_INNOV-007-F_112025_0001_00042
     *
     * @param  Enrollment $enrollment   Enrollment Eloquent (peut être une instance chargée)
     * @param  string|null $countryCode Code pays (par défaut CI)
     * @return Reference
     */
    public function generateForEnrollment(Enrollment $enrollment, ?string $countryCode = 'CI'): Reference
    {
        // Idempotence: si une référence existe déjà, on la retourne
        if ($enrollment->reference()->exists()) {
            return $enrollment->reference;
        }

        // Pré-chargement des relations nécessaires
        $enrollment->loadMissing(['session.training.pillar', 'participant']);

        $session   = $enrollment->session;
        $training  = $session->training;
        $pillar    = $training->pillar;
        $participant = $enrollment->participant;

        // Garde robuste sur end_date
        $dt = Carbon::parse($session->end_date ?? now());
        $monthYear = $dt->format('mY');

        // doc_kind uniformisé
        $docKind = $training->has_exam ? 'CER' : 'ATT';

        // Code formation : custom_code si custom, sinon code
        $trainingCode = ($training->is_custom && $training->custom_code)
            ? $training->custom_code
            : $training->code;

        // Compteurs atomiques (évite MAX+1 et les races)
        $globalInc = AtomicCounter::next("GLOBAL:{$monthYear}");
        $pillarInc = AtomicCounter::next("PILLAR:{$pillar->id}:{$monthYear}");

        // Construction de la référence
        $referenceString = $this->formatReference(
            countryCode: strtoupper($countryCode ?? 'CI'),
            docKind: $docKind,
            trainingCode: $trainingCode,
            monthYear: $monthYear,
            pillarIncrement: $pillarInc,
            globalIncrement: $globalInc
        );

        // Création de l'enregistrement Reference
        return Reference::create([
            'doc_kind'         => $docKind,
            'reference'        => $referenceString,
            'month_year'       => $monthYear,
            'global_increment' => $globalInc,
            'pillar_increment' => $pillarInc,
            'pillar_id'        => $pillar->id,
            'enrollment_id'    => $enrollment->id,
            'meta'             => [
                'country_code'     => strtoupper($countryCode ?? 'CI'),
                'training_code'    => $trainingCode,
                'training_title'   => $training->title,
                'pillar_name'      => $pillar->name,
                'participant_name' => $participant?->full_name,
                'participant_id'   => $participant?->id,
                'session_id'       => $session->id,
                'session_date'     => $dt->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Génère des références pour TOUTES les inscriptions d'une session.
     * Renvoie la liste des références créées (n’englobe pas celles déjà existantes).
     *
     * @param  Session $session
     * @param  string|null $countryCode
     * @return array<Reference>
     */
    public function generateForSession(Session $session, ?string $countryCode = 'CI'): array
    {
        $session->loadMissing(['enrollments.participant', 'training.pillar']);

        $created = [];

        // Ici, pas besoin de grosse transaction : l'allocation est atomique au niveau des compteurs
        foreach ($session->enrollments as $enrollment) {
            if ($enrollment->reference()->exists()) {
                continue; // idempotence
            }
            $created[] = $this->generateForEnrollment($enrollment, $countryCode);
        }

        return $created;
    }

    /**
     * Génère des références pour un tableau d'IDs d'enrollments.
     *
     * @param  array<int> $enrollmentIds
     * @param  string|null $countryCode
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
     * Vérifie si une référence existe et renvoie l'objet enrichi.
     * (Sans gestion d'alias ici — à brancher si vous avez un modèle LegacyAlias.)
     *
     * @param  string $referenceNumber
     * @return Reference|null
     */
    public function verifyReference(string $referenceNumber): ?Reference
    {
        return Reference::where('reference', strtoupper($referenceNumber))
            ->with(['enrollment.participant', 'enrollment.session.training.pillar'])
            ->first();
    }

    /**
     * Export “tableau 2D” (prêt pour Excel) des références sélectionnées ou de toutes.
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

        $data = [];
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
     * Statistiques rapides basées sur les nouvelles colonnes indexées.
     *
     * @return array{
     *   total_references:int,
     *   total_certificates:int,
     *   total_attestations:int,
     *   by_pillar:\Illuminate\Support\Collection,
     *   by_month:\Illuminate\Support\Collection
     * }
     */
    public function getStatistics(): array
    {
        $total      = Reference::count();
        $totalCert  = Reference::where('doc_kind', 'CER')->count();
        $totalAtt   = Reference::where('doc_kind', 'ATT')->count();

        // Stat par pilier (utilise pillar_id dénormalisé pour aller vite)
        $byPillar = DB::table('references')
            ->join('pillars', 'references.pillar_id', '=', 'pillars.id')
            ->select('pillars.name', DB::raw('COUNT(*) AS count'))
            ->groupBy('pillars.id', 'pillars.name')
            ->orderByDesc('count')
            ->get();

        // Stat par mois (MMYYYY)
        $byMonth = DB::table('references')
            ->select('month_year', DB::raw('COUNT(*) AS count'))
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get();

        return [
            'total_references'   => $total,
            'total_certificates' => $totalCert,
            'total_attestations' => $totalAtt,
            'by_pillar'          => $byPillar,
            'by_month'           => $byMonth,
        ];
    }

    /**
     * Formatteur central du numéro de référence.
     * Facile à changer si la règle évolue.
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
}