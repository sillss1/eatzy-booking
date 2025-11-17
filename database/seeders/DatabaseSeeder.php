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
        if (file_exists($creationPath)) {
            $creation = file_get_contents($creationPath);
            DB::unprepared($creation);
            $this->command->info('✓ creation.sql executed successfully');
        }
        
        // Read and execute population.sql
        $populationPath = database_path('population.sql');
        if (file_exists($populationPath)) {
            $population = file_get_contents($populationPath);
            DB::unprepared($population);
            $this->command->info('✓ population.sql executed successfully');
        }
    }
}


// namespace Database\Seeders;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;
// class DatabaseSeeder extends Seeder
// {
//     /**
//      * Runs database/thingy-seed.sql as-is.
//      * The SQL reads current_setting('app.schema', true) and defaults to 'thingy'.
//      */
//     public function run(): void
//     {
//         // Get schema name from environment (e.g., .env or .env.testing)
//         $schema = env('DB_SCHEMA');
//         // Load the raw SQL file
//         $path = base_path('database/thingy-seed.sql');
//         $sql = file_get_contents($path);
//         // If DB_SCHEMA is set, expose it to the SQL script
//         // (the script reads it via current_setting('app.schema', true))
//         if ($schema !== null) {
//             DB::statement("SELECT set_config('app.schema', ?, false)", [$schema]);
//         }
//         // Run the SQL script
//         DB::unprepared($sql);
//         // Show a message in the Artisan console
//         $this->command?->info('Database seeded using schema: ' . ($schema ?? 'thingy (default)'));
//     }
// }