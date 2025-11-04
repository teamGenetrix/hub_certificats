<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed pillars first (required for trainings)
        $this->call([
            PillarSeeder::class,
            TrainingSeeder::class,
        ]);

        // Create default admin user
        User::factory()->create([
            'name' => 'Admin Genetrix',
            'email' => 'admin@genetrix.com',
        ]);
    }
}
