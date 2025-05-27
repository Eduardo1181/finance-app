@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container">
  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h5 class="card-title">Chave da API</h5>
      <p class="text-muted small">Sua chave nunca será exibida publicamente. Apenas você pode visualizá-la ou alterá-la.</p>
      <form method="POST" action="{{ route('profile.updateApiKey') }}">
        @csrf
        <div class="mb-3">
          <label for="api_key" class="form-label">Nova Chave de API:</label>
          <input type="password" class="form-control" name="api_key" id="api_key" placeholder="**************" required>
        </div>
        <button type="submit" class="btn btn-sm btn-secondary">Salvar Chave</button>
      </form>
    </div>
  </div>
  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h5 class="card-title">Ações no Topo</h5>
      <p class="text-muted">Escolha as ações que você quer ver sempre no topo da tela.</p>
      <div class="mb-3">
        <input type="text" id="company-search" class="form-control" placeholder="Buscar empresa (ex: Apple)">
        <ul id="results-list" class="list-group mt-2"></ul>
      </div>
      <div id="selected-symbols" class="mb-3 d-flex flex-wrap gap-2"></div>
      <form method="POST" action="{{ route('profile.updateFavorites') }}">
        @csrf
        <div id="hidden-inputs"></div>
        <button type="submit" class="btn btn-sm btn-success">Salvar Ações Favoritas</button>
      </form>
    </div>
  </div>
</div>
<script>
  let selectedSymbols = [];

  $(document).ready(function () {
    @if (Auth::user() && !Auth::user()->api_key)
      ModalHelper.loadLargeModal('{{ route('modal.api_help') }}', false);
    @endif

    $('#company-search').on('input', function () {
      const query = $(this).val();
      if (query.length < 2) return;

      $.ajax({
        url: '/api/search-company',
        data: { query },
        success: function (data) {
          let html = '';
          data.forEach(company => {
            if (!selectedSymbols.includes(company.symbol)) {
              html += `<li class="list-group-item list-group-item-action" data-symbol="${company.symbol}">
                        ${company.name} (${company.symbol})
                      </li>`;
            }
          });
          $('#results-list').html(html);
        }
      });
    });

    $('#results-list').on('click', 'li', function () {
      const symbol = $(this).data('symbol');
      if (!selectedSymbols.includes(symbol)) {
        selectedSymbols.push(symbol);
        updateSelectedSymbols();
      }
      $('#company-search').val('');
      $('#results-list').empty();
    });

    $('#selected-symbols').on('click', '.remove-symbol', function () {
      const symbolToRemove = $(this).data('symbol');
      selectedSymbols = selectedSymbols.filter(s => s !== symbolToRemove);
      updateSelectedSymbols();
    });

    function updateSelectedSymbols() {
      let chips = '';
      let inputs = '';
      selectedSymbols.forEach(symbol => {
        chips += `<span class="badge bg-primary p-2 d-inline-flex align-items-center">
                    ${symbol}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-2 remove-symbol" data-symbol="${symbol}"></button>
                  </span>`;
        inputs += `<input type="hidden" name="symbols[]" value="${symbol}">`;
      });

      $('#selected-symbols').html(chips);
      $('#hidden-inputs').html(inputs);
    }
  });
</script>
@endsection