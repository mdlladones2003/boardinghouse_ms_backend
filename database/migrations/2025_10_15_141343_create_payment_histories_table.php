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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id('payment_history_id');
            $table->foreignId('payment_id')
                  ->nullable()
                  ->index()
                  ->constrained('payments', 'payment_id')
                  ->onDelete('cascade')
                  ->name('payment_histories_payment_id_foreign');
            $table->foreignId('user_id')
                  ->nullable()
                  ->index()
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade')
                  ->name('payment_histories_user_id_foreign');
            $table->string('action', 255);
            $table->enum('action_type', ['created', 'updated', 'cancelled']);
            $table->timestamp('action_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
