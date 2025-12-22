<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $schema = env('DB_SCHEMA', 'lbaw25145');

        // Set the search path to your schema
        DB::statement("SET search_path TO {$schema}");

        // Read and execute creation.sql
        $creationPath = database_path('creation.sql');
        if (file_exists($creationPath) && !\Illuminate\Support\Facades\Schema::hasTable('user')) {
            $creation = file_get_contents($creationPath);
            DB::unprepared($creation);
            $this->command->info('✓ creation.sql executed successfully');
        }

        // Read and execute population.sql
        $populationPath = database_path('population.sql');
        if (file_exists($populationPath)) {
            // Disable triggers during population to avoid validation errors (e.g. modifying past reservations)
            DB::statement("SET session_replication_role = 'replica';");

            try {
                $population = file_get_contents($populationPath);
                DB::unprepared($population);
                $this->command->info('✓ population.sql executed successfully');
            } finally {
                // Re-enable triggers
                DB::statement("SET session_replication_role = 'origin';");
            }
        }
    }
}