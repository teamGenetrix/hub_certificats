<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ⚠️ Complète la liste ci-dessous avec les entrées du catalogue
        // Format: code (du catalogue), title (intitulé), has_exam (bool), is_custom (bool), custom_code (optionnel)
        // Exemple: ["code" => "LSS-GB", "title" => "Lean Six Sigma - Green Belt", "has_exam" => true]
        $programs = [
            // ====== EXEMPLES À ADAPTER / COMPLÉTER ======
            // ["code" => "LSS-YB", "title" => "Lean Six Sigma - Yellow Belt", "has_exam" => true,  "is_custom" => false, "custom_code" => null],
            // ["code" => "LSS-GB", "title" => "Lean Six Sigma - Green Belt",  "has_exam" => true,  "is_custom" => false, "custom_code" => null],
            // ["code" => "LSS-BB", "title" => "Lean Six Sigma - Black Belt",  "has_exam" => true,  "is_custom" => false, "custom_code" => null],
            // ["code" => "AGILE-101-F", "title" => "Fondamentaux Agile/Scrum", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            // ["code" => "CUSTOM-ACME-001", "title" => "Programme Custom ACME - Excellence Opé", "has_exam" => false, "is_custom" => true, "custom_code" => "ACME-EXO-001"],
            // ============================================

            // 💡 Ajoute ici toutes les formations officielles du PDF catalogue
            // Pilier 1 : Planification Strategique
            ["code" => "STRAT-001-F", "title" => "Stratégie Gagnante : Élaboration et Exécution d'un Plan Stratégique Performant", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-002-F", "title" => "Définition, Assignation et Communication d'Objectifs Stratégiques Fonctionnels et Individuels", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-003-F", "title" => "Introduction à l'Excellence Opérationnelle pour Managers", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-004-F", "title" => "Comment Intégrer la Stratégie d'Excellence Opérationnelle à la Stratégie d'Entreprise ?", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-005-F", "title" => "Comment Identifier, Sélectionner et Attribuer des Projets d'Amélioration Continue ?", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-006-F", "title" => "Gestion de Projets de Transformation pour Managers : Stratégies de Gouvernance de Projets
pour une Transformation Réussie", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-007-F", "title" => "Gestion du Temps et des Priorités", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "STRAT-008-F", "title" => "Gestion de Risques", "has_exam" => false, "is_custom" => false, "custom_code" => null],


            // Pilier 2 : Gestion des processus
            ["code" => "PROC-001-F", "title" => "Le Travail Standard pour Améliorer la Performance", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "PROC-002-F", "title" => "L'Approche Processus : Maximiser l'Efficience par une Gestion Optimale des Activités Quotidiennes", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "PROC-003-F", "title" => "La Maitrise Statistique des Processus", "has_exam" => false, "is_custom" => false, "custom_code" => null],


            //Pilier 3 : Gestion de la performance
            ["code" => "PERF-001-F", "title" => "Mettre en Œuvre un Système de Pilotage de la Performance Efficace", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "PERF-002-F", "title" => "Management Visuelle pour une Meilleure Gestion de la Performance", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "PERF-003-F", "title" => "Collecte, Analyse, Interprétation et Présentation des Données pour un Processus
de Prise de Décision Efficient", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "PERF-004-F", "title" => "Optimisation des Réunions Opérationnelles : Stratégies pour une Efficience Maximale", "has_exam" => false, "is_custom" => false, "custom_code" => null],

            //Innovation
            ["code" => "INNOV-001-F", "title" => "Design For Six Sigma Yellow Belt", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-002-F", "title" => "Design For Six Sigma Green Belt", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-003-F", "title" => "Design For Six Sigma Black Belt", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-004-F", "title" => "Management de l'Innovation", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-005-F", "title" => "Innover avec le Design Thinking", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-006-F", "title" => "Faciliter une Démarche de Design Thinking", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "INNOV-007-F", "title" => "Créativité : Mettre l'idéation au service de l'organisation", "has_exam" => false, "is_custom" => false, "custom_code" => null],


            //Pilier : Amelioration continue
            ["code" => "AMCONT-001-F", "title" => "Lean Foundation ou Introduction au Lean Management", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-002-F", "title" => "Lean IT foundation", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-003-F", "title" => "Lean Practionner", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-004-F", "title" => "Lean Master", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-005-F", "title" => "Lean Value Stream Mapping", "has_exam" => false, "is_custom" => false, "custom_code" => null],

            ["code" => "AMCONT-006-F", "title" => "Lean Six Sigma White Belt", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-007-F", "title" => "Lean Six Sigma Yellow Belt", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-008-F", "title" => "Lean Six Sigma Green Belt", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-009-F", "title" => "Lean Six Sigma Black Belt", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-010-F", "title" => "Lean Six Sigma Master Black Belt", "has_exam" => true, "is_custom" => false, "custom_code" => null],

            ["code" => "AMCONT-011-F", "title" => "Lean Six Sigma Champion or Sponsor", "has_exam" => true, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-012-F", "title" => "La Méthodologie 8D", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-013-F", "title" => "Le \"A3 Thinking\" pour Résoudre les Problèmes Simples au Quotidien", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-014-F", "title" => "Le Toyota Kata : Comment Améliorer la Performance de Votre Organisation au Quotidien", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-015-F", "title" => "Les 7 Outils de la Qualité", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-016-F", "title" => "Le Management des Projets Lean Six Sigma", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-017-F", "title" => "Introduction à l'Excellence Opérationnelle pour Managers", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-018-F", "title" => "Comment Intégrer la Stratégie d'Excellence Opérationnelle à la Stratégie d'Entreprise ?", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-019-F", "title" => "Comment Identifier, Sélectionner et Attribuer des Projets d'Amélioration Continue ?", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-020-F", "title" => "La Méthodologie 5S ou Comment Maitriser son Environnement de Travail", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "AMCONT-021-F", "title" => "Methode SMED", "has_exam" => false, "is_custom" => false, "custom_code" => null],


            //Pilier : Leadership, motivation et engagement 
            ["code" => "CAPHUM-001-F", "title" => "Les enjeux et les Impacts de l'Intelligence Artificielle dans l'Environnement des Affaires", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-002-F", "title" => "Leadership et Gestion d'Équipe : Organiser et Coordonner pour Atteindre les Objectifs", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-003-F", "title" => "Développer l'Intelligence Collective dans votre Organisation", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-004-F", "title" => "Animer des Ateliers d'Intelligence Collective", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-005-F", "title" => "L'Art de Présenter – Boostez vos Présentations !", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-006-F", "title" => "Apprendre à Coacher ses Collaborateurs", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-007-F", "title" => "Piloter et accompagner le changement", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CAPHUM-008-F", "title" => "Prévenir et Gérer les Conflits", "has_exam" => false, "is_custom" => false, "custom_code" => null],

            //Pilier : Conduite du changement
            ["code" => "CONDCHG-001-F", "title" => "Réussir la Conduite du Changement, Démarche et Outils", "has_exam" => false, "is_custom" => false, "custom_code" => null],
            ["code" => "CONDCHG-002-F", "title" => "Piloter et Accompagner le Changement dans l'Entreprise", "has_exam" => false, "is_custom" => false, "custom_code" => null],
        ];

        // Normalisation minimale + valeurs par défaut
        $now = now();
        $rows = array_map(function (array $p) use ($now) {
            $p = array_merge([
                'code'        => null,
                'title'       => null,
                'has_exam'    => false,
                'is_custom'   => false,
                'custom_code' => null,
            ], $p);

            if (!$p['code'] || !$p['title']) {
                throw new \InvalidArgumentException("Chaque programme doit avoir 'code' et 'title'. Problème sur: " . json_encode($p));
            }

            return [
                'code'         => Str::upper(trim($p['code'])),
                'title'        => trim($p['title']),
                'has_exam'     => (bool) $p['has_exam'],
                'is_custom'    => (bool) $p['is_custom'],
                'custom_code'  => $p['custom_code'] ? Str::upper(trim($p['custom_code'])) : null,
                'metadata'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }, $programs);

        // Upsert par 'code' (unique) — met à jour title/has_exam/is_custom/custom_code
        DB::table('programs')->upsert(
            $rows,
            uniqueBy: ['code'],
            update: ['title', 'has_exam', 'is_custom', 'custom_code', 'updated_at']
        );
    }
}
