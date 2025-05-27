@extends('layouts.app') 
@section('content')
<style>
.ml-1 {
	margin-right: 10px;
}

.summary-container {
	display: flex;
	gap: 20px;
	margin: 20px 0 20px;
	flex-wrap: wrap;
}

.card-box {
	flex: 1;
	max-width: 100%;
	background-color: #f8f9fa;
	padding: 20px;
	border: 1px solid #bababa;
	border-radius: 20px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.card-box h5 {
	margin-bottom: 15px;
	font-weight: bold;
}

.card-box .value {
	font-size: 17px;
	color: #333;
	margin-bottom: 20px;
}

.card-box .actions {
	display: flex;
	gap: 10px;
}

.card-box .btn {
	flex: 1;
}

.car-box-total {
	justify-content: center;
	text-align: center;
}

.btn-finance {
	background: #333;
	color: #f1f1f1;
	max-width: 100px;
}

#transactionsWrapper {
	max-height: 470px;
	overflow-y: auto;
}

.dates {
	border-radius: 10px;
}

.card-action {
	position: relative;
	cursor: pointer;
	transition: transform 0.2s ease;
	overflow: hidden;
}

.card-action:hover {
	transform: scale(1.02);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-action .hover-icon {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	opacity: 0;
	transition: opacity 0.2s ease;
	font-size: 3rem;
	color: rgba(0, 0, 0, 0.15);
	pointer-events: none;
}

.card-action:hover .hover-icon {
	opacity: 1;
}
</style>
<x-top-bar />
<div class="container">
	<div class="d-flex my-3">
    <select class="dates form-select w-auto" name="dates" id="dates">
			<option value="">Mês atual</option>
			@foreach ($months as $month)
				<option value="{{ $month['value'] }}" {{ $month['selected'] ? 'selected' : '' }}>
					{{ $month['label'] }}
				</option>
			@endforeach
    </select>
	</div>
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
		<div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.installments') }}')">
			<h5>Parcelado</h5>
			<div class="value text-danger">
				{{ $installments !== '0,00' ? 'R$ ' . $installments : 'Sem parcelas' }}
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
		<div class="card-box card-action" onclick="ModalHelper.loadModal('{{ route('modal.credit_card') }}')">
			<h5>Cartão de Crédito</h5>
			<div class="value text-danger">
				{{ $totalCreditcard !== '0,00' ? 'R$ ' . $totalCreditcard : 'Sem transações' }}
			</div>
			<div class="hover-icon">
				<i class="bi bi-plus-lg"></i>
			</div>
		</div>
		<div class="card-box car-box-total">
			<h5>Total em Carteira</h5>
			@if($walletBalance < 0)
			<span class="value text-success"> R$ {{ $walletBalance }}</span>
			@else
			<span class="value text-danger">Negativo: R$ {{ $walletBalance }}</span>
			@endif
		</div>
	</div>
	<div class="card mt-4">
		<div class="card-body">
			<div class="mb-3 d-flex justify-content-between">
				<div>
					<h5 class="card-title">Transações</h5>
				</div>
				<div>
					<a href="#" id="btnExportCsv" class="btn btn-outline-success btn-sm me-2">
						<i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
					</a>
					<a href="#" id="btnExportPdf" class="btn btn-outline-danger btn-sm">
						<i class="bi bi-file-earmark-pdf"></i> Exportar PDF
					</a>
				</div>	
			</div>				
			<div class="row mb-3">
				<div class="col-md-3">
					<label for="typeFilter">Tipo de transação</label>
					<select id="typeFilter" class="form-select">
						<option value="">Todos os tipos</option>
						<option value="Entrada">Entrada</option>
						<option value="Aporte">Aporte</option>
						<option value="Despesa">Despesa</option>
						<option value="Saída">Saída</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="descriptionFilter">Descrição</label>
					<input type="text" id="descriptionFilter" class="form-control" placeholder="Descrição">
				</div>
				<div class="col-md-3">
					<label for="startDate">De</label>
					<input type="date" id="startDate" class="form-control" placeholder="De">
				</div>
				<div class="col-md-3">
					<label for="endDate">Até</label>
					<input type="date" id="endDate" class="form-control" placeholder="Até">
				</div>
			</div>
			<div id="transactionsWrapper">
				<table class="table table-striped" id="transactionsTable">
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
						@foreach($transactions as $transiction)
							<tr data-date="{{ $transiction['date'] ?? '' }}">
								<td>{{ $transiction['type'] ?? 'Sem tipo' }}</td>
								<td>{{ $transiction['category'] ?? 'Sem categoria' }}</td>
								<td>{{ $transiction['dateDisplay'] ?? 'Sem data' }}</td>
								<td>R$ {{ $transiction['amount'] ?? '0,00' }}</td>
								<td>{{ $transiction['description'] ?? 'Sem descrição' }}</td>
							</tr>
						@endforeach
					</tbody>						
				</table>					
			</div>			
		</div>
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
        $('.card-box:contains("Entrada") .value').text(
          data.totalIncomes !== '0,00' ? 'R$ ' + data.totalIncomes : 'Sem entradas'
        );
        $('.card-box:contains("Aporte") .value').text(
          data.totalDeposits !== '0,00' ? 'R$ ' + data.totalDeposits : 'Sem aportes'
        );
        $('.card-box:contains("Saída") .value').text(
          data.totalWithdrawals !== '0,00' ? 'R$ ' + data.totalWithdrawals : 'Sem saídas'
        );
        $('.card-box:contains("Despesas") .value').text(
          data.totalExpenses !== '0,00' ? 'R$ ' + data.totalExpenses : 'Sem despesas'
        );
        $('.car-box-total .value').text(
          data.walletBalance !== '0,00' ? 'R$ ' + data.walletBalance : 'Sem saldo'
        );
				$('.card-box:contains("Parcelado") .value').text(
					data.totalInstallments !== '0,00' ? 'R$ ' + data.totalInstallments : 'Sem parcelas'
				);
				$('.card-box:contains("Cartão de Crédito") .value').text(
					data.totalCreditCards !== '0,00' ? 'R$ ' + data.totalCreditCards : 'Sem transações'
				);

        const tbody = $('#transactionsTable tbody');
        tbody.empty();

        if (data.transactions && data.transactions.length > 0) {
          data.transactions.forEach(t => {
            const formattedDate = t.dateDisplay.split('/').reverse().join('-');
            tbody.append(`
              <tr data-date="${formattedDate}">
                <td>${t.type}</td>
                <td>${t.category ?? 'Sem categoria'}</td>
                <td>${t.dateDisplay}</td>
                <td>R$ ${t.amount}</td>
                <td>${t.description}</td>
              </tr>
            `);
          });
        } else {
          tbody.append('<tr><td colspan="5" class="text-center">Nenhuma transação neste mês</td></tr>');
        }
        filterTable();
      },
      error: function () {
        alert('Erro ao buscar dados mensais.');
      }
    });
  });

  $('#dates').trigger('change');
  function filterTable() {
    const type = $('#typeFilter').val().toLowerCase();
    const desc = $('#descriptionFilter').val().toLowerCase();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    $('#transactionsTable tbody tr').each(function () {
      const row = $(this);
      const rowType = row.find('td:eq(0)').text().toLowerCase();
      const rowDesc = row.find('td:eq(4)').text().toLowerCase();
      const rawDate = row.find('td:eq(2)').text().trim();
      const parts = rawDate.split('/');

      let rowDate = '';
      if (parts.length === 3) {
        rowDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
      }

      const typeMatch = !type || rowType.includes(type);
      const descMatch = !desc || rowDesc.includes(desc);
      const startMatch = !startDate || rowDate >= startDate;
      const endMatch = !endDate || rowDate <= endDate;

      row.toggle(typeMatch && descMatch && startMatch && endMatch);
    });
  }

  $('#typeFilter, #descriptionFilter, #startDate, #endDate').on('change keyup', filterTable);

  $('#btnExportCsv').on('click', function (e) {
    e.preventDefault();
    const month = $('#dates').val() || new Date().getMonth() + 1;
    window.location.href = `/export/csv?month=${month}`;
  });

  $('#btnExportPdf').on('click', function (e) {
    e.preventDefault();
    const month = $('#dates').val() || new Date().getMonth() + 1;
    window.location.href = `/export/pdf?month=${month}`;
  });
});
</script>
@endsection