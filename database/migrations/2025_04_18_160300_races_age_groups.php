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
        Schema::create('race_age_group', function (Blueprint $table): void {
            $table->foreignId('race_id')->constrained()->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained()->onDelete('cascade');
            // This will delete the pivot record when either race or age group is deleted
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
