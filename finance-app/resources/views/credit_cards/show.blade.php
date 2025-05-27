@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container my-5">
  <div class="mb-4">
    <h3 class="h3 fw-semibold mb-2">Cartão {{ $creditCard->name }}</h3>
    <p class="text-muted small">
      Dia do fechamento: {{ $creditCard->closing_day }} |
      Dia do pagamento: {{ $creditCard->payment_day }} |
      Débito automático: {{ $creditCard->auto_debit ? 'Sim' : 'Não' }}
    </p>
  </div>
  <div class="row mb-3">
    <div>
      <div class="card shadow-sm">
        <div class="card-body">
          <p class="text-muted small mb-1">Limite total:</p>
          <h5 class="card-title fw-bold">R$ {{ number_format($creditCard->limit_amount, 2, ',', '.') }}</h5>
          <p class="text-muted small mb-1">Disponível:</p>
          <h5 class="card-title fw-bold text-success">R$ {{ number_format($creditCard->available_limit, 2, ',', '.') }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="h5 fw-semibold mb-0">Transações</h5>
    <button 
      class="btn btn-sm btn-outline-danger btn-sm" 
      onclick="ModalHelper.loadModal('{{ route('modal.credit_card', ['card' => $creditCard->id]) }}')"
    >
      <i class="bi bi-credit-card-2-back"></i> Adicionar
    </button>
    <form method="GET" action="{{ route('credit_cards.show', $creditCard) }}">
      <select name="month" id="month" class="form-select form-select-sm" onchange="this.form.submit()">
        @foreach ($months as $m)
          <option value="{{ $m['value'] }}" {{ $m['selected'] ? 'selected' : '' }}>
            {{ $m['label'] }}
          </option>
        @endforeach
      </select>
    </form>    
  </div>
  @if ($allTransactions->count())
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th class="text-end">Valor</th>
            <th class="text-center">Parcela</th>
            <th class="text-center">Status</th>
            <th class="text-center">Ação</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($allTransactions as $description => $grouped)
            @php
              $main = $grouped->firstWhere(function($p) use ($month, $year) {
                  return \Carbon\Carbon::parse($p->purchase_date)->month == $month
                      && \Carbon\Carbon::parse($p->purchase_date)->year == $year;
              });
            @endphp
            @if ($main)
              <tr class="main-row" data-group="{{ md5($description) }}" style="cursor: pointer;">
                <td>{{ \Carbon\Carbon::parse($main->purchase_date)->format('d/m/Y') }}</td>
                <td>{{ $main->description ?? '-' }}</td>
                <td class="text-end">R$ {{ number_format($main->amount, 2, ',', '.') }}</td>
                <td class="text-center">
                  @if ($main->is_installment)
                    {{ $main->installment_number }}/{{ $main->total_installments }}
                  @else
                    —
                  @endif
                </td>
                <td class="text-center">
                  @if ($main->is_paid)
                    <span class="badge bg-success">Paga</span>
                  @else
                    <span class="badge bg-danger">Pendente</span>
                  @endif
                </td>
                <td class="text-center">
                  @if (!$main->is_paid)
                    <form action="{{ route('credit_card_transactions.pay', $main) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success">Pagar</button>
                    </form>
                  @else
                    —
                  @endif
                </td>
              </tr>
              @foreach ($grouped as $txn)
                @if ($txn->id !== $main->id)
                  <tr class="sub-row" data-group="{{ md5($description) }}" style="display: none;">
                    <td>-> {{ \Carbon\Carbon::parse($txn->purchase_date)->format('d/m/Y') }}</td>
                    <td>{{ $txn->description ?? '-' }}</td>
                    <td class="text-end">R$ {{ number_format($txn->amount, 2, ',', '.') }}</td>
                    <td class="text-center">
                      @if ($txn->is_installment)
                        {{ $txn->installment_number }}/{{ $txn->total_installments }}
                      @else
                        —
                      @endif
                    </td>
                    <td class="text-center">
                      @if ($txn->is_paid)
                        <span class="badge bg-success">Paga</span>
                      @else
                        <span class="badge bg-danger">Pendente</span>
                      @endif
                    </td>
                    <td class="text-center">
                      @if (!$txn->is_paid)
                        <form action="{{ route('credit_card_transactions.pay', $txn) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success">Pagar</button>
                        </form>
                      @else
                        —
                      @endif
                    </td>
                  </tr>
                @endif
              @endforeach
            @endif
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                Nenhuma transação encontrada para este mês.
              </td>
            </tr>
          @endforelse
        </tbody>        
      </table>
    </div>
  </div>
  @else
    <p class="text-muted text-center">Nenhuma transação encontrada para este cartão.</p>
  @endif
</div>
<script>
  $(document).ready(function () {
    $('.main-row').click(function () {
      var group = $(this).data('group');
      $('.sub-row[data-group="' + group + '"]').toggle();
    });
    $('form button').click(function (e) {
      e.stopPropagation();
    });
  });
</script>
@endsection
