<h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Criar Usuário</h5>
<hr>
<form action="{{ route('admin.users.store') }}" method="POST">
  @csrf
  <div class="row g-2">
    <div class="col-md">
      <input name="name" class="form-control" placeholder="Nome" required>
    </div>
    <div class="col-md">
      <input name="email" type="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="col-md">
      <input name="password" type="password" class="form-control" placeholder="Senha" required>
    </div>
    <div class="col-auto align-self-center">
      <div class="form-check mb-0">
        <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin">
        <label class="form-check-label" for="is_admin">Admin</label>
      </div>
    </div>
    <div class="col-auto">
      <button class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> Criar
      </button>
    </div>
  </div>
</form>