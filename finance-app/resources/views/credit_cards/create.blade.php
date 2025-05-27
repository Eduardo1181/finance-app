@extends('layouts.app')

@section('content')
<x-top-bar />
<div class="container my-5" style="max-width: 600px;">
	<h1 class="h3 fw-semibold mb-4">Adicionar Novo Cartão</h1>
	<form action="{{ route('credit_cards.store') }}" method="POST">
		@csrf
		<div class="mb-3">
			<label for="name" class="form-label">Nome do Cartão</label>
			<input type="text" name="name" id="name" class="form-control" required>
		</div>
		<div class="mb-3">
			<label for="type" class="form-label">Tipo do Cartão</label>
			<select name="type" id="type" class="form-select" required>
				<option value="normal">Normal</option>
				<option value="gold">Gold</option>
				<option value="black">Black</option>
				<option value="diamond">Diamond</option>
			</select>
		</div>		
		<div class="mb-3">
			<label for="limit_amount" class="form-label">Limite</label>
			<input type="number" name="limit_amount" id="limit_amount" class="form-control" step="0.01" required>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3">
				<label for="closing_day" class="form-label">Dia do Fechamento</label>
				<input type="number" name="closing_day" id="closing_day" class="form-control" min="1" max="31" required>
			</div>
			<div class="col-md-6 mb-3">
				<label for="payment_day" class="form-label">Dia do Pagamento</label>
				<input type="number" name="payment_day" id="payment_day" class="form-control" min="1" max="31" required>
			</div>
		</div>
		<div class="form-check mb-3">
			<input type="checkbox" name="auto_debit" id="auto_debit" class="form-check-input">
			<label for="auto_debit" class="form-check-label">Ativar Débito Automático</label>
		</div>
		<div>
			<button type="submit" class="btn btn-primary">Salvar Cartão</button>
		</div>
	</form>
</div>
@endsection
