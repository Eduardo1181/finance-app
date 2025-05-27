<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    foreach (['incomes', 'deposits', 'expenses', 'withdrawals'] as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
      });
    }
  }

  public function down(): void
  {
    foreach (['incomes', 'deposits', 'expenses', 'withdrawals'] as $table) {
      Schema::table($table, function (Blueprint $table) {
        $table->dropForeign([$table->getTable().'_category_id_foreign']);
        $table->dropColumn('category_id');
      });
    }
  }
};
