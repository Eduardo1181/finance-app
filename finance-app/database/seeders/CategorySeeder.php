<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
  public function run(): void
  {
    $categories = [
      ['name' => 'Salário', 'type' => 'Entrada'],
      ['name' => 'Freelance', 'type' => 'Entrada'],
      ['name' => 'Reembolso', 'type' => 'Entrada'],
      ['name' => 'Venda', 'type' => 'Entrada'],

      ['name' => 'Investimento em ações', 'type' => 'Aporte'],
      ['name' => 'Renda fixa', 'type' => 'Aporte'],
      ['name' => 'Renda passiva', 'type' => 'Aporte'],
      ['name' => 'Cripto', 'type' => 'Aporte'],

      ['name' => 'Alimentação', 'type' => 'Despesa'],
      ['name' => 'Transporte', 'type' => 'Despesa'],
      ['name' => 'Moradia', 'type' => 'Despesa'],
      ['name' => 'Lazer', 'type' => 'Despesa'],
      ['name' => 'Academia', 'type' => 'Despesa'],
      ['name' => 'Saúde', 'type' => 'Despesa'],

      ['name' => 'Transferência bancária', 'type' => 'Saída'],
      ['name' => 'Saque', 'type' => 'Saída'],
      ['name' => 'Pix', 'type' => 'Saída'],
    ];

    foreach ($categories as $category) {
      Category::firstOrCreate($category);
    }
  }
}
