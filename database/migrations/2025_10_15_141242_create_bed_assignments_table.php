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
        Schema::create('bed_assignments', function (Blueprint $table) {
            $table->id('bed_assignment_id');
            $table->foreignId('room_id')
                  ->index()
                  ->constrained('rooms', 'room_id')
                  ->onDelete('cascade')
                  ->name('bed_assignment_room_id_foreign');
            $table->foreignId('tenant_id')
                  ->index()
                  ->constrained('tenants', 'tenant_id')
                  ->onDelete('cascade')
                  ->name('bed_assignments_tenant_id_foreign');
            $table->integer('bed_number');
            $table->date('assigned_on');
            $table->enum('status', ['assigned', 'vacant']);
            $table->timestamps();

            $table->unique(['room_id', 'bed_number']);
            $table->unique(['tenant_id', 'bed_assignment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_assignments');
    }
};
