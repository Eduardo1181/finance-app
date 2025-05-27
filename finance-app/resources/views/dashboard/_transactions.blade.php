<div class="card mt-4">
  <div class="card-body">
    <div class="mb-3 d-flex justify-content-end align-items-center">
      <h5 class="card-title me-auto">Transações</h5>
      <a href="#" id="btnExportCsv" class="btn btn-outline-success btn-sm me-2">
        <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
      </a>
      <a href="#" id="btnExportPdf" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
      </a>
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
        <input type="date" id="startDate" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="endDate">Até</label>
        <input type="date" id="endDate" class="form-control">
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
