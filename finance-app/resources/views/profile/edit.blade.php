@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container mt-3">
  @foreach (['profile_updated', 'password_updated', 'success'] as $msg)
    @if (session($msg))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session($msg) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    @endif
  @endforeach
  <h5 class="mb-3">Gerenciar Perfil</h5>
  <p class="text-muted small">Atualize suas informações, senha, email e nome</p>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Editar Perfil</h5>
          <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="name" class="form-label">Nome</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title fw-semibold mb-4">Alterar Senha</h5>
          <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label for="current_password" class="form-label">Senha Atual</label>
              <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Nova Senha</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
              <input type="password" class="form-control" name="password_confirmation" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-warning">Alterar Senha</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection