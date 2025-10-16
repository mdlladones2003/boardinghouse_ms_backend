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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->foreignId('tenant_id')
                  ->nullable()
                  ->constrained('tenants', 'tenant_id')
                  ->onDelete('cascade');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('email')->unique();
            $table->enum('role', ['tenant', 'admin', 'staff']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
