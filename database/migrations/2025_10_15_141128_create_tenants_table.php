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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id('tenant_id');
            $table->foreignId('room_id')
                  ->nullable()
                  ->index()
                  ->constrained('rooms', 'room_id')
                  ->onDelete('cascade')
                  ->name('tenants_room_id_foreign');
            $table->foreignId('address_id')
                  ->nullable()
                  ->index()
                  ->constrained('addresses', 'address_id')
                  ->onDelete('cascade')
                  ->name('tenants_address_id_foreign');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20);
            $table->date('move_in');
            $table->enum('status', ['active', 'inactive', 'evicted']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
