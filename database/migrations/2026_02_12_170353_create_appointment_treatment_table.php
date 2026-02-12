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
        Schema::create('appointment_treatment', function (Blueprint $table) {
            $table->uuid('appointment_id');
            $table->foreign('appointment_id', 'fk_appointment_treatment_appointment')
                ->references('id')->on('appointments')->onDelete('cascade');

            $table->uuid('treatment_id')->index();
            $table->foreign('treatment_id', 'fk_appointment_treatment_treatment')
                ->references('id')->on('treatments')->onDelete('cascade');

            $table->timestamps();

            $table->primary(['appointment_id', 'treatment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_treatment');
    }
};
