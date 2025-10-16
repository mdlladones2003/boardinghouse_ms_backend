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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('tenant_id')
                  ->index()
                  ->constrained('tenants', 'tenant_id')
                  ->onDelete('cascade')
                  ->name('payments_tenant_id_foreign');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', ['cash', 'credit_card', 'bank_transfer', 'online']);
            $table->enum('status', ['completed', 'pending', 'failed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
