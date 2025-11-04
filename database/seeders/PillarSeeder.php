<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pillars = [
            [
                'name' => 'Planification Stratégique',
                'code' => 'STRAT',
                'description' => 'Élaboration et exécution de plans stratégiques performants, définition d\'objectifs et intégration de l\'excellence opérationnelle.',
            ],
            [
                'name' => 'Gestion des Processus',
                'code' => 'PROC',
                'description' => 'Optimisation des processus, travail standard et maîtrise statistique pour améliorer la performance.',
            ],
            [
                'name' => 'Gestion de la Performance',
                'code' => 'PERF',
                'description' => 'Mise en œuvre de systèmes de pilotage, management visuel et analyse de données pour la prise de décision.',
            ],
            [
                'name' => 'Innovation',
                'code' => 'INNOV',
                'description' => 'Design For Six Sigma, Design Thinking, management de l\'innovation et créativité au service de l\'organisation.',
            ],
            [
                'name' => 'Amélioration Continue',
                'code' => 'AMCONT',
                'description' => 'Lean Management, Six Sigma, méthodologies de résolution de problèmes et excellence opérationnelle.',
            ],
            [
                'name' => 'Leadership, Motivation et Engagement',
                'code' => 'CAPHUM',
                'description' => 'Développement du leadership, intelligence collective, gestion d\'équipe et compétences humaines.',
            ],
            [
                'name' => 'Conduite du Changement',
                'code' => 'CONDCHG',
                'description' => 'Pilotage et accompagnement du changement, démarche et outils pour une transformation réussie.',
            ],
            [
                'name' => 'Design Organisationnel',
                'code' => 'DESORG',
                'description' => 'Design Thinking, Lean Management, Six Sigma, méthodologies de résolution de problèmes et excellence opérationnelle.',
            ],
        ];

        $now = now();
        
        foreach ($pillars as $pillar) {
            DB::table('pillars')->updateOrInsert(
                ['code' => $pillar['code']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $pillar['name'],
                    'code' => $pillar['code'],
                    'description' => $pillar['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
