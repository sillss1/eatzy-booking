<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $creationPath = database_path('creation.sql');

        if (file_exists($creationPath)) {
            $creation = file_get_contents($creationPath);
            // Execute the raw SQL schema
            DB::unprepared($creation);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping tables manually or relying on migrate:fresh to wipe everything
        // Since the schema contains many tables, we might just leave it blank 
        // as this is a base schema migration.
        // A proper down would drop all tables created by creation.sql
        // For now, doing nothing is acceptable for a "legacy schema import" migration.
    }
};
