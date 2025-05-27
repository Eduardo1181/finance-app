<style>
  input {
    border-radius: 4px !important;
  }
</style>
<h4 class="modal-title">Novo Aporte</h4>
<form method="POST" action="{{ route('deposits.store') }}">
  @csrf
  <div class="mb-3">
    <label for="category_id">Categoria</label>
    <select name="category_id" class="form-control" id="categorySelect" onchange="handleCategoryChange()">
      <option value="">Selecione</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
      @endforeach
      <option value="other">Outra...</option>
    </select>
  </div>
  <div class="mb-3 d-none" id="customCategoryDiv">
    <label for="custom_category">Nova Categoria</label>
    <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Digite o nome da nova categoria">
  </div>
  <div class="mb-3">
    <label for="expense_date">Data do Aporte</label>
    <input type="date" name="expense_date" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="amount">Valor do Aporte (R$)</label>
    <input type="number" step="0.01" name="amount" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="monthly_rate">Rendimento Mensal (%)</label>
    <input type="number" step="0.01" name="monthly_rate" class="form-control" placeholder="Ex: 1.5">
  </div>
  <div class="mb-3">
    <label for="duration_months">Duração (meses)</label>
    <input type="number" name="duration_months" class="form-control" placeholder="Ex: 12">
  </div>
  <div class="mb-3">
    <label for="start_date">Data de Início do Rendimento</label>
    <input type="date" name="start_date" class="form-control">
  </div>
  <div class="form-check mb-3">
    <input type="checkbox" name="is_locked" class="form-check-input" id="is_locked">
    <label class="form-check-label" for="is_locked">Bloquear durante o período</label>
  </div>
  <div class="mb-3">
    <label for="description">Descrição</label>
    <textarea name="description" class="form-control" rows="2"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Salvar Aporte</button>
</form>
<script>
function handleCategoryChange() {
    const select = document.getElementById('categorySelect');
    const customDiv = document.getElementById('customCategoryDiv');

    if (select.value === 'other') {
        customDiv.classList.remove('d-none');
    } else {
        customDiv.classList.add('d-none');
    }
}
</script>