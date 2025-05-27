@extends('layouts.app')
@section('content')
<x-top-bar />
<div class="container py-4">
  <h5 class="mb-4">Gerenciamento de Usuários</h5>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  <div class="row g-2 mb-4 align-items-end">
		<div class="col-md-4">
			<label class="form-label small">Filtrar por Nome</label>
			<input 
				type="text" 
				name="name" 
				form="form-search" 
				class="form-control form-control-sm" 
				placeholder="Digite um nome" 
				value="{{ request('name') }}"
			>
		</div>
		<div class="col-md-4">
			<label class="form-label small">Filtrar por Email</label>
			<input 
				type="email" 
				name="email" 
				form="form-search" 
				class="form-control form-control-sm" 
				placeholder="Digite um email" 
				value="{{ request('email') }}"
			>
		</div>
		<div class="col-md-4">
			<div class="d-flex gap-2">
				<button 
					type="submit" 
					form="form-search" 
					class="btn btn-outline-primary btn-sm flex-fill"
				>
					<i class="bi bi-search"></i> Buscar
				</button>
				<button 
					onclick="ModalHelper.loadLargeModal('{{ route('modal.create_user') }}')" 
					class="btn btn-success btn-sm flex-fill"
				>
					<i class="bi bi-person-plus-fill"></i> Novo Usuário
				</button>
			</div>
		</div>
	</div>	
  <form id="form-search" method="GET" action="{{ route('admin.users.index') }}"></form>
  <div class="card">
    <div class="card-header">
      <strong><i class="bi bi-people-fill"></i> Lista de Usuários</strong>
      <span class="text-muted float-end">
        {{ $users->count() }} de {{ $users->total() }} encontrados
      </span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Admin</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr @if(auth()->id() === $user->id) class="table-secondary" @endif>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                @if((bool)$user->is_admin)
                  <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sim</span>
                @else
                  <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Não</span>
                @endif
              </td>
              <td>
                @if(auth()->id() === $user->id)
                  <em>Você</em>
                @else
                  <div class="btn-group btn-group-sm" role="group">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="is_admin" value="{{ (bool)$user->is_admin ? 0 : 1 }}">
                      <button class="btn btn-sm btn-outline-warning" style="margin-right: 5px;" title="Alterar admin">
                        <i class="bi {{ $user->is_admin ? 'bi-person-dash' : 'bi-person-plus' }}"></i>
                      </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.block', $user) }}">
                      @csrf @method('PATCH')
                      <input type="hidden" name="is_blocked" value="{{ (bool)$user->is_blocked ? 0 : 1 }}">
                      <button class="btn btn-sm btn-outline-secondary" style="margin-right: 5px;" title="Bloquear/Desbloquear">
                        <i class="bi {{ $user->is_blocked ? 'bi-unlock' : 'bi-lock' }}"></i>
                      </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Excluir {{ addslashes($user->name) }}?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" title="Excluir">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center">Nenhum usuário encontrado.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection