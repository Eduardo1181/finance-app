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
        Schema::create('credit_card_transactions', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('credit_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        
            $table->decimal('amount', 10, 2);
            $table->date('purchase_date');
            $table->text('description')->nullable();
        
            $table->boolean('is_installment')->default(false);
            $table->integer('installment_number')->nullable();       // Número da parcela (1, 2, 3...)
            $table->integer('total_installments')->nullable();        // Total de parcelas
        
            $table->boolean('is_paid')->default(false);              // Já foi quitado?
            $table->timestamp('paid_at')->nullable();                // Data do pagamento (manual ou via fatura)
        
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_card_transactions');
    }
};
