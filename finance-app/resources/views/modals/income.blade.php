<style>
  input {
    border-radius: 4px !important;
  }
</style>
<h4 class="modal-title">Nova Entrada</h4>
<form action="{{ route('incomes.store') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label for="category_id" class="form-label">Categoria</label>
    <select name="category_id" id="category_id" class="form-select">
      <option value="">Selecione uma categoria</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label for="expense_date">Data</label>
    <input type="date" name="expense_date" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="amount">Valor</label>
    <input type="number" name="amount" step="0.01" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="description">Descrição</label>
    <input type="text" name="description" class="form-control">
  </div>
  <button type="submit" class="btn btn-success">Salvar Entrada</button>
</form>