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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('patient_id')->index();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->uuid('dentist_id')->index();
            $table->foreign('dentist_id')->references('id')->on('dentists')->onDelete('cascade');

            $table->dateTime('start')->index();
            $table->dateTime('end')->index();
            $table->smallInteger('duration');

            $table->timestamps();

            $table->unique(['patient_id', 'dentist_id', 'start', 'end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
