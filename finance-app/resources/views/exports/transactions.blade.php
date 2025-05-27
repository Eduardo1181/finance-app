<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #333;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .green {
      color: green;
    }

    .red {
      color: red;
    }

    .summary-table {
      margin-top: 30px;
    }

    .summary-table td {
      padding: 6px 10px;
    }
  </style>
</head>
<body>
  <h2>Relatório de Transações - {{ now()->translatedFormat('F/Y') }}</h2>
  <table>
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Categoria</th>
        <th>Data</th>
        <th>Valor</th>
        <th>Descrição</th>
      </tr>
    </thead>
    <tbody>
      @php
        $totals = [
          'income' => 0,
          'expense' => 0,
          'deposit' => 0,
          'withdraw' => 0
        ];

        $typeMap = [
          'Entrada' => 'income',
          'Despesa' => 'expense',
          'Aporte' => 'deposit',
          'Saída' => 'withdraw'
        ];
      @endphp
      @foreach ($transactions as $t)
        @php
          $amount = floatval(str_replace(',', '.', str_replace('.', '', $t['amount'])));
          $typeLabel = $t['type'];
          $type = $typeMap[$typeLabel] ?? 'other';

          if (isset($totals[$type])) {
            $totals[$type] += $amount;
          }

          $isPositive = in_array($type, ['income', 'deposit']);
          $valueClass = $isPositive ? 'green' : 'red';
        @endphp
        <tr>
          <td>{{ $typeLabel }}</td>
          <td>{{ $t['category'] ?? 'Sem categoria' }}</td>
          <td>{{ $t['dateDisplay'] }}</td>
          <td class="{{ $valueClass }}">R$ {{ $t['amount'] }}</td>
          <td>{{ $t['description'] ?? '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  @php
    $saldo = ($totals['income'] + $totals['deposit']) - ($totals['expense'] + $totals['withdraw']);
  @endphp
  <table class="summary-table">
    <tr>
      <td>Entradas</td>
      <td class="green">R$ {{ number_format($totals['income'], 2, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Despesas</td>
      <td class="red">R$ {{ number_format($totals['expense'], 2, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Aportes</td>
      <td class="green">R$ {{ number_format($totals['deposit'], 2, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Saídas</td>
      <td class="red">R$ {{ number_format($totals['withdraw'], 2, ',', '.') }}</td>
    </tr>
    <tr>
      <td>Saldo Final</td>
      <td class="{{ $saldo >= 0 ? 'green' : 'red' }}">
        R$ {{ number_format($saldo, 2, ',', '.') }}
      </td>
    </tr>
  </table>
  <p style="text-align: center; margin-top: 30px;">Gerado em {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>