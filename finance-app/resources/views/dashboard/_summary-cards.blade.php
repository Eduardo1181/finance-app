<div class="summary-container">
  <div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.income') }}')">
    <h5>Entrada</h5>
    <div class="value text-success">
      {{ $totalIncomes !== '0,00' ? 'R$ ' . $totalIncomes : 'Sem entradas' }}
    </div>
    <div class="hover-icon">
      <i class="bi bi-plus-lg"></i>
    </div>
  </div>
  <div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.deposit') }}')">
    <h5>Aporte</h5>
    <div class="value text-success">
      {{ $totalDeposits !== '0,00' ? 'R$ ' . $totalDeposits : 'Sem aportes' }}
    </div>
    <div class="hover-icon">
      <i class="bi bi-plus-lg"></i>
    </div>
  </div>
  <div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.withdrawal') }}')">
    <h5>Saída</h5>
    <div class="value text-danger">
      {{ $totalWithdrawals !== '0,00' ? 'R$ ' . $totalWithdrawals : 'Sem saídas' }}
    </div>
    <div class="hover-icon">
      <i class="bi bi-dash-lg"></i>
    </div>
  </div>
  <div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.expense') }}')">
    <h5>Despesas</h5>
    <div class="value text-danger">
      {{ $totalExpenses !== '0,00' ? 'R$ ' . $totalExpenses : 'Sem despesas' }}
    </div>
    <div class="hover-icon">
      <i class="bi bi-dash-lg"></i>
    </div>
  </div>
  <div class="card-box car-box-total">
    <h5>Total em Carteira</h5>
    @if($walletBalance < 0)
      <span class="value text-danger">Negativo: R$ {{ $walletBalance }}</span>
    @else
      <span class="value text-success">R$ {{ $walletBalance }}</span>
    @endif
  </div>
</div>
