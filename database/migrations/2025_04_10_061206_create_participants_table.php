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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('lname');
            $table->string('email')->unique(); // email() isn't a valid column type
            $table->string('phone', 15)->nullable(); // no 'number' type; string is better for phone
            $table->date('dob'); // stick to lowercase for consistency
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->foreignId('race_id')->constrained()->onDelete('cascade');
            $table->foreignId('age_group_id')->constrained()->onDelete('cascade');
            /* $table->unsignedInteger('bib_number')->nullable(); */
            /* $table->unsignedInteger('chip_number')->nullable(); */

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
