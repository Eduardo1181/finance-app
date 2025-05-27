@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container">
  <div class="d-flex my-3">
    <select class="dates form-select w-auto" name="dates" id="dates">
      <option value="{{ now()->month }}">Mês atual</option>
      @foreach ($months as $month)
        <option value="{{ $month['value'] }}" data-label="{{ $month['label'] }}" {{ $month['selected'] ? 'selected' : '' }}>
          {{ $month['label'] }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="row justify-content-center g-4">
    <div>
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title">
            Resumo do mês (<span id="monthLabel">{{ $months->firstWhere('selected', true)['label'] }}</span>/{{ now()->year }})
          </h5>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
              <span>Entradas</span>
              <span class="text-success" id="totalIncomes">R$ {{ $totalIncomes }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Despesas</span>
              <span class="text-danger" id="totalExpenses">R$ {{ $totalExpenses }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Aportes</span>
              <span class="text-success" id="totalDeposits">R$ {{ $totalDeposits }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Saídas</span>
              <span class="text-danger" id="totalWithdrawals">R$ {{ $totalWithdrawals }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Parcelado</span>
              <span class="text-danger" id="totalInstallments">R$ {{ $totalInstallments ?? '0,00' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Cartão de Crédito</span>
              <span class="text-danger" id="totalCreditCards">R$ {{ $totalCreditCards ?? '0,00' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between fw-bold">
              <span>Saldo Atual</span>
              <span id="walletBalance" class="text-{{ $walletBalance < 0 ? 'danger' : 'success' }}">
                R$ {{ $walletBalance }}
              </span>
            </li>
          </ul>          
        </div>
      </div>
    </div>
  </div>
  <hr class="my-4">
  <div class="d-flex flex-wrap gap-2 justify-content-between">
    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-primary btn-sm flex-grow-1">
      <i class="bi bi-graph-up"></i> Gráfico financeiro
    </a>
    <a href="{{ route('modal.income') }}" class="btn btn-outline-success btn-sm flex-grow-1" onclick="event.preventDefault(); ModalHelper.loadModal(this.href);">
      <i class="bi bi-plus-circle"></i> Nova Entrada
    </a>
    <a href="{{ route('modal.expense') }}" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="event.preventDefault(); ModalHelper.loadModal(this.href);">
      <i class="bi bi-dash-circle"></i> Nova Despesa
    </a>
    <a href="{{ route('modal.credit_card') }}" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="event.preventDefault(); ModalHelper.loadModal(this.href);">
      <i class="bi bi-credit-card-2-back"></i> Cartão de Crédito
    </a>
    <a href="{{ route('modal.installments') }}" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="event.preventDefault(); ModalHelper.loadModal(this.href);">
      <i class="bi bi-card-list"></i> Recorrente
    </a>
    <a href="#" id="exportPdf" class="btn btn-outline-dark btn-sm flex-grow-1">
      <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
    </a>
  </div>
</div>
<script>
$(document).ready(function () {
  $('#dates').on('change', function () {
    const month = $(this).val();
    $.ajax({
      url: '/dashboard/monthly-data',
      type: 'GET',
      data: { month },
      success: function (data) {
        $('#totalIncomes').text(data.totalIncomes !== '0,00' ? 'R$ ' + data.totalIncomes : 'Sem entradas');
        $('#totalExpenses').text(data.totalExpenses !== '0,00' ? 'R$ ' + data.totalExpenses : 'Sem despesas');
        $('#totalDeposits').text(data.totalDeposits !== '0,00' ? 'R$ ' + data.totalDeposits : 'Sem aportes');
        $('#totalWithdrawals').text(data.totalWithdrawals !== '0,00' ? 'R$ ' + data.totalWithdrawals : 'Sem saídas');
        $('#totalInstallments').text(data.totalInstallments !== '0,00' ? 'R$ ' + data.totalInstallments : 'Sem contas parceladas');
        $('#totalCreditCards').text(data.totalCreditCards !== '0,00' ? 'R$ ' + data.totalCreditCards : 'Sem lançamento de cartão');
        const selectedOption = $('#dates option:selected');
        let monthName = selectedOption.data('label');
        if (!monthName) {
          const currentDate = new Date();
          monthName = new Intl.DateTimeFormat('pt-BR', { month: 'long' }).format(currentDate);
        }
        $('#monthLabel').text(monthName.charAt(0).toUpperCase() + monthName.slice(1));
        const balanceText = data.walletBalance !== '0,00' ? 'R$ ' + data.walletBalance : 'Sem saldo';
        $('#walletBalance').text(balanceText);
        if (parseFloat(data.walletBalance.replace('.', '').replace(',', '.')) < 0) {
          $('#walletBalance').removeClass('text-success').addClass('text-danger');
        } else {
          $('#walletBalance').removeClass('text-danger').addClass('text-success');
        }
      },
      error: function () {
        alert('Erro ao buscar dados mensais');
      }
    });
  });

  $('#exportPdf').click(function(e) {
    e.preventDefault();
    const month = $('#dates').val();
    window.location.href = '/export/pdf?month=' + month;
  });
});
</script>
@endsection