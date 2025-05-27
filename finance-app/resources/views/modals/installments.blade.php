<style>
  input, select {
    border-radius: 4px !important;
  }
</style>
<h4 class="modal-title">Novo Lançamento Parcelado</h4>
<form action="{{ route('recorrente.store') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label for="type" class="form-label">Tipo</label>
    <select name="type" class="form-select" required>
      <option value="">Selecione</option>
      <option value="expense">Despesa</option>
      <option value="income">Entrada</option>
      <option value="deposit">Aporte</option>
      <option value="withdrawal">Saída</option>
    </select>
  </div>
  <div class="mb-3">
    <label for="category_id" class="form-label">Categoria</label>
    <select name="category_id" class="form-select">
      <option value="">Sem categoria</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label for="start_date">Data inicial</label>
    <input type="date" name="start_date" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="amount">Valor da parcela</label>
    <input type="number" name="amount" step="0.01" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="installments">Quantidade de parcelas</label>
    <input type="number" name="installments" class="form-control" min="1" required>
  </div>
  <div class="mb-3">
    <label for="due_day">Dia de vencimento da parcela</label>
    <input type="number" name="due_day" class="form-control" min="1" max="31" required>
  </div>
  <div class="mb-3">
    <label for="description">Descrição</label>
    <input type="text" name="description" class="form-control">
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" name="auto_debit" id="auto_debit" class="form-check-input" value="1">
    <label for="auto_debit" class="form-check-label">Débito automático no vencimento</label>
  </div>
  <input type="hidden" name="wallet_id" value="{{ auth()->user()->wallet->id }}">
  <button type="submit" class="btn btn-primary">Salvar Parcelado</button>
</form>