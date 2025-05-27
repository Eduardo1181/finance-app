@extends('layouts.app')

@section('content')
<style>
  .perspective {
    perspective: 1000px;
  }

  .card-container {
    width: 100%;
    height: 250px;
  }

  .card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.9s;
  }

	.card-container:hover .card-inner {
		transform: rotateY(180deg);
	}

  .card-front,
  .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border: none;
  }

  .card-back {
    transform: rotateY(180deg);
  }

  .card-gold {
		background: linear-gradient(135deg, #cba135, #e1bc59);
		color: #000;
		box-shadow: 0 0 12px rgba(203, 161, 53, 0.5);
	}

	.card-diamond {
		background-color: #54555A;
		background-size: cover;
		color: #fff;
		box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
	}

	.card-normal {
		background: linear-gradient(135deg, #0072c6, #00c2cb);
		color: #fff;
		box-shadow: 0 0 12px rgba(0, 128, 255, 0.4);
	}

	.card-black {
		background: #1c1c1c;
		color: #fff;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
	}

</style>
<x-top-bar />
<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="h4 fw-semibold">Meus cartões</h1>	
		<a href="{{ route('credit_cards.create') }}" class="btn btn-outline-dark btn-sm">+ Novo cartão</a>
	</div>
	@if ($cards->count())
	<div class="container mt-4">
		<div class="row g-4">
			@foreach ($cards as $card)
			<div class="col-12 col-md-6 col-lg-3">
				<div class="card-container perspective">
					<div class="card-inner">
						<div class="card {{ getCardColorClass($card->type) }} rounded-4 shadow h-100 p-4 card-front">
							<div class="d-flex flex-column justify-content-between h-100">
								<div>
									<h5 class="text-uppercase fw-bold">{{ $card->name }}</h5>
								</div>
								<div class="mt-auto d-flex justify-content-between align-items-center">
									<span class="small">{{ ucfirst($card->type) }}</span>
								</div>
							</div>
						</div>
						<div class="card {{ getCardColorClass($card->type) }} rounded-4 shadow h-100 p-4 card-back">
							<div class="d-flex flex-column justify-content-between h-100">
								<div class="d-flex justify-content-between">
									<div>
										<p class="mb-1 small">Limite total</p>
										<h5 class="fw-bold">R$ {{ number_format($card->limit_amount, 2, ',', '.') }}</h5>
										<p class="mb-0 small">
											Disponível:
											<strong>R$ {{ number_format($card->available_limit, 2, ',', '.') }}</strong>
										</p>
									</div>
									<div class="text-end">
										<p class="mb-1 small">
											Fechamento: dia {{ $card->closing_day }}<br>
											Pagamento: dia {{ $card->payment_day }}
										</p>
									</div>
								</div>
								<div class="mt-auto text-end pt-3">
									<a href="{{ route('credit_cards.show', $card) }}" class="btn btn-outline-light btn-sm">
										Ver detalhes
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>						
			@endforeach
		</div>
	</div>		
	@else
		<p class="text-muted">Nenhum cartão de crédito cadastrado ainda.</p>
	@endif
</div>
@endsection