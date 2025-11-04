<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin par défaut
        User::firstOrCreate(
            ['email' => 'admin@genetrix.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('✓ Utilisateur admin créé avec succès');
        $this->command->info('  Email: admin@genetrix.com');
        $this->command->info('  Mot de passe: admin123');
        $this->command->warn('⚠ Pensez à changer le mot de passe par défaut en production !');
    }
}
