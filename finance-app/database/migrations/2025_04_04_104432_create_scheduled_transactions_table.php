<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('scheduled_transactions', function (Blueprint $table) {
          $table->id();
          $table->foreignId('recurring_template_id')->constrained()->onDelete('cascade');
          $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
          $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
          $table->decimal('amount', 10, 2);
          $table->date('due_date');
          $table->enum('status', ['pendente', 'pago', 'vencido'])->default('pendente');
          $table->integer('installment_number');
          $table->integer('total_installments');
          $table->timestamp('paid_at')->nullable();
          $table->boolean('is_auto_debit')->default(false);
          $table->timestamps();
        });
      }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_transactions');
    }
};
