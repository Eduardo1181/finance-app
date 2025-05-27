@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container py-4">
  <h5 class="mb-3">Parcelas Agendadas</h5>
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif
  <form method="GET" action="{{ route('installments') }}" class="mb-4">
    <label for="month" class="form-label">Filtrar por mês:</label>
    <select name="month" id="month" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
      @foreach ($months as $m)
        <option value="{{ $m['value'] }}" {{ $m['selected'] ? 'selected' : '' }}>
          {{ $m['label'] }}
        </option>
      @endforeach
    </select>
  </form>
  <div class="d-flex justify-content-between flex-wrap mb-4" style="gap: 1rem;">
    <div class="card-box card-action" style="min-width: 200px;">
      <span>Total Parcelado</span>
      <div class="value text-danger fs-5">
        {{ $totalInstallments !== '0,00' ? 'R$ ' . $totalInstallments : 'Sem parcelas' }}
      </div>
    </div>
    <div class="card-box card-action" style="min-width: 200px;">
      <a href="{{ route('modal.installments') }}" class="btn btn-outline-danger btn-sm w-100" onclick="event.preventDefault(); ModalHelper.loadModal(this.href);">
        <i class="bi bi-card-list"></i> Recorrente
      </a>
    </div>
    <div class="card-box card-action" style="min-width: 200px;">
      <span>Parcelas Vencidas</span>
      <div class="value text-danger fs-5">
        {{ $totalVencidas !== '0,00' ? 'R$ ' . $totalVencidas : 'Nenhuma vencida' }}
      </div>
    </div>
  </div>  
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Descrição</th>
          <th>Categoria</th>
          <th>Valor</th>
          <th>Vencimento</th>
          <th>Parcela</th>
          <th>Status</th>
          <th>Débito automático</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($todasParcelas as $templateId => $parcelas)
          @php
            $main = $parcelas->firstWhere(fn($p) => \Carbon\Carbon::parse($p->due_date)->month == $month);
          @endphp
          @if ($main)
            <tr class="main-row" data-template="{{ $templateId }}" style="cursor: pointer;">
              <td>{{ $main->template->description }}</td>
              <td>{{ optional($main->category)->name ?? 'Sem categoria' }}</td>
              <td class="text-danger">R$ {{ number_format($main->amount, 2, ',', '.') }}</td>
              <td>{{ \Carbon\Carbon::parse($main->due_date)->format('d/m/Y') }}</td>
              <td>{{ $main->installment_number }}/{{ $main->total_installments }}</td>
              <td>{{ ucfirst($main->status) }}</td>
              <td>{{ $main->is_auto_debit ? 'Sim' : 'Não' }}</td>
              <td>
                @if ($main->status === 'pendente')
                  <form action="{{ route('parcelas.confirmar', $main->id) }}" method="POST" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" title="Pagar agora">
                      Pagar
                    </button>
                  </form>
                @elseif ($main->status === 'pago')
                  <span class="text-success">Pago</span>
                @else
                  —
                @endif
              </td>
            </tr>
          @endif
          @foreach ($parcelas as $p)
            @if (!$main || $p->id !== $main->id)
              <tr class="sub-row" data-template="{{ $templateId }}" style="display: none;">
                <td>->{{ $p->template->description }}</td>
                <td>{{ optional($p->category)->name ?? '—' }}</td>
                <td class="text-danger">R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($p->due_date)->format('d/m/Y') }}</td>
                <td>{{ $p->installment_number }}/{{ $p->total_installments }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>{{ $p->is_auto_debit ? 'Sim' : 'Não' }}</td>
                <td>
                  @if ($p->status === 'pendente')
                    <form action="{{ route('parcelas.confirmar', $p->id) }}" method="POST" class="mb-0">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success" title="Pagar agora">
                       Pagar
                      </button>
                    </form>
                  @elseif ($p->status === 'pago')
                    <span class="text-success">Pago</span>
                  @else
                    —
                  @endif
                </td>
              </tr>
            @endif
          @endforeach
        @empty
          <tr><td colspan="8" class="text-center">Nenhuma parcela encontrada neste mês</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<script>
  $(document).ready(function () {
    $('.main-row').on('click', function () {
      const templateId = $(this).data('template');
      console.log('Clicou no template:', templateId);
      const $subRows = $(`.sub-row[data-template="${templateId}"]`);
      $subRows.toggle();
    });
    $('form button').on('click', function (e) {
      e.stopPropagation();
    });
  });
</script>
@endsection