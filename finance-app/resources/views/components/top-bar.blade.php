<style>
  .top-bar {
    top: 0;
    left: 0;
    width: 100%;
    max-width: 1840px;
    background-color: #f8f9fa;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #bababa;
    align-items: center;
    padding: 10px 20px;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }

  #stock-ticker {
    overflow: hidden;
    white-space: nowrap;
    flex: 1;
    margin-right: 20px;
  }

  #ticker-content {
    display: inline-block;
    padding-left: 100%;
    animation: scrollRight 25s linear infinite;
    font-size: 14px;
  }

  .mr-1 {
    margin-right: 10px;
  }

  
  .drop-user {
    margin-top: 7px !important;
  }

  @keyframes scrollRight {
    from { transform: translateX(0%); }
    to   { transform: translateX(-100%); }
  }

  .form-select {
    border-radius: 16px;
  }
</style>

<div class="top-bar">
  <div id="stock-ticker">
    <span id="ticker-content"></span>
  </div>
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none"
       id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
       <span class="mr-1">Bem-vindo, {{ Auth::user()->name }}</span>
      <img src="{{ asset('logo.svg') }}" alt="Usuário" width="40" height="40" class="rounded-circle me-2">
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow text-sm drop-user" aria-labelledby="userDropdown">
      <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
          <i class="bi bi-person-circle"></i> Perfil
        </a>
      </li>
      @if(auth()->user()->is_admin)
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.users.index') }}">
            <i class="bi bi-person-fill-gear"></i> Usuários
          </a>
        </li>
      @endif
      <li>
        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.stock') }}">
          <i class="bi bi-key-fill"></i> Configurar API
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-right"></i> Sair
          </button>
        </form>
      </li>
    </ul>
  </div>
</div>
<script>
  $(document).ready(function () {
    $.ajax({
      url: '/api/stocks',
      type: 'GET',
      success: function (data, status, xhr) {
        const content = $('#ticker-content');
        content.empty();

        if (xhr.status === 204 || !Array.isArray(data) || data.length === 0) {
          content.html(`<span style="color: gray;">Configure suas ações favoritas no perfil para acompanhar o mercado aqui.</span>`);
          return;
        }

        let html = '';
        data.forEach(stock => {
          const arrow = stock.changesPercentage > 0 ? '📈' : '📉';
          const color = stock.changesPercentage > 0 ? 'green' : 'red';
          html += `<span style="margin-right: 30px; color: ${color};">
            ${arrow} ${stock.symbol}: R$${stock.price.toFixed(2)} (${stock.changesPercentage.toFixed(2)}%)
            ${stock.alert ? `<strong style="margin-left: 5px;">${stock.alert}</strong>` : ''}
          </span>`;
        });

        content.html(html);
      },
      error: function (xhr) {
        const content = $('#ticker-content');
        content.empty();

        if (xhr.status === 403 && xhr.responseJSON?.error?.includes('chave de API')) {
          content.html(`
            <span style="color: gray;">
              Você ainda não configurou sua chave de API. 
              <a href="{{ route('profile.stock') }}" class="text-primary text-decoration-underline">Clique aqui para configurar.</a>
            </span>
          `);
        } else {
          content.html('<span style="color: red;">Erro ao carregar ações</span>');
        }
      }
    });
  });
</script>