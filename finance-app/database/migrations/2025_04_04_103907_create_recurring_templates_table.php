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
    Schema::create('recurring_templates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        $table->enum('type', ['income', 'expense', 'deposit', 'withdrawal']);
        $table->decimal('amount', 10, 2);
        $table->string('description')->nullable();
        $table->date('start_date');
        $table->integer('installments');
        $table->integer('due_day');
        $table->boolean('auto_debit')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_templates');
    }
};
