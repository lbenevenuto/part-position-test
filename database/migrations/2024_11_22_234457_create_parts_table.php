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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Episode::class)->constrained()->cascadeOnDelete();
            $table->tinyInteger('position');
            $table->string('title');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE parts DROP CONSTRAINT IF EXISTS parts_episode_id_position_unique, ADD CONSTRAINT parts_episode_id_position_unique UNIQUE (episode_id, position) DEFERRABLE INITIALLY DEFERRED;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
