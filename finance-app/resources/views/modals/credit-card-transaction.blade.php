<style>
  input, select {
    border-radius: 4px !important;
  }
</style>
<h4 class="modal-title">Nova Transação no Cartão</h4>
<form action="{{ route('credit_card_transactions.store') }}" method="POST">
  @csrf
  @isset($creditCard)
    <input type="hidden" name="credit_card_id" value="{{ $creditCard->id }}">
    <div class="mb-3">
      <label class="form-label">Cartão</label>
      <input type="text" class="form-control" value="{{ $creditCard->name }}" disabled>
    </div>
  @else
    <div class="mb-3">
      <label for="credit_card_id" class="form-label">Cartão</label>
      <select name="credit_card_id" class="form-select" required>
        <option value="">Selecione</option>
        @foreach($creditCards as $card)
          <option value="{{ $card->id }}">{{ $card->name }}</option>
        @endforeach
      </select>
    </div>
  @endisset
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
    <label for="purchase_date" class="form-label">Data da Compra</label>
    <input type="date" name="purchase_date" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="amount" class="form-label">Valor da Compra</label>
    <input type="number" name="amount" step="0.01" class="form-control" required>
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Descrição</label>
    <input type="text" name="description" class="form-control">
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" name="is_installment" id="is_installment" class="form-check-input" value="1">
    <label for="is_installment" class="form-check-label">Compra Parcelada</label>
  </div>
  <div id="installment-fields" style="display: none;">
    <div class="mb-3">
      <label for="total_installments" class="form-label">Número de Parcelas</label>
      <input type="number" name="total_installments" class="form-control" min="1">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Salvar Transação</button>
</form>
<script>
$(document).ready(function () {
  const $checkbox = $('#is_installment');
  const $installmentFields = $('#installment-fields');

  $checkbox.on('change', function () {
    if ($(this).is(':checked')) {
      $installmentFields.show();
    } else {
      $installmentFields.hide();
    }
  });
});
</script>