<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Torna as colunas user_id opcionais (NULL)
        DB::statement('ALTER TABLE review ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE reply ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE reservation ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE waitlist ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE notification ALTER COLUMN user_id DROP NOT NULL');
        // Se a tabela restaurant existir e tiver owner_id:
        // DB::statement('ALTER TABLE restaurant ALTER COLUMN owner_id DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: Reverter para NOT NULL
        // DB::statement('ALTER TABLE review ALTER COLUMN user_id SET NOT NULL');
        // DB::statement('ALTER TABLE reply ALTER COLUMN user_id SET NOT NULL');
        // DB::statement('ALTER TABLE reservation ALTER COLUMN user_id SET NOT NULL');
        // DB::statement('ALTER TABLE waitlist ALTER COLUMN user_id SET NOT NULL');
        // DB::statement('ALTER TABLE notification ALTER COLUMN user_id SET NOT NULL');
    }
};
