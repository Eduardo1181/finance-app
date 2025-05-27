<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Finance App</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
  <script type="module" src="{{ vite_asset('resources/js/app.js') }}"></script>

  <style>
    body {
      background-color: #f9f9fe;
    }

    a {
      color: black !important;
    }

    .sidebar {
      transition: width 0.3s ease;
      color: white;
      height: 100vh;
      position: fixed;
      padding: 1rem;
      overflow-x: hidden;
      z-index: 1;
    }
    .sidebar a, .sidebar button {
      display: flex;
      align-items: center;
      color: white;
      padding: 10px 0;
      text-decoration: none;
      background: none;
      border: none;
      width: 100%;
      text-align: left;
    }
    .sidebar.collapsed {
      width: 80px;
    }
    .sidebar.expanded {
      width: 267px;
    }
    #main-content {
      transition: margin-left 0.3s ease;
      width: 100%;
    }
    .expanded-content {
      margin-left: 267px;
    }
    .collapsed-content {
      margin-left: 80px;
    }
    .container {
      max-width: 100%;
    }
    input {
      border-radius: 4px !important;
    }
  </style>
</head>
<body x-data="{ isExpanded: false, openCharts: false }" class="d-flex">
  <div id="sidebar"
       :class="isExpanded ? 'sidebar expanded bg-dark text-white p-3' : 'sidebar collapsed bg-dark text-white p-3'">
    <a href="#" class="text-white d-flex" @click.prevent="isExpanded = !isExpanded">
      <i :class="isExpanded ? 'bi bi-arrow-left fs-5' : 'bi bi-list fs-5'"></i>
    </a>
    <template x-if="isExpanded">
      <a href="/">
        <h3 class="text-white">Finance App</h3>
      </a>
    </template>
    <a href="{{ route('home') }}" class="text-white d-flex align-items-center mb-3">
      <i class="bi bi-house-door-fill fs-5"></i>
      <template x-if="isExpanded"><span class="ms-2">Início</span></template>
    </a>
    <div>
      <a href="#"
         @click.prevent="openCharts = !openCharts"
         class="text-white d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-pie-chart fs-5"></i>
          <template x-if="isExpanded"><span class="ms-2">Gráficos</span></template>
        </div>
        <template x-if="isExpanded">
          <i class="bi" :class="openCharts ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
        </template>
      </a>
      <template x-if="openCharts && isExpanded">
        <div class="ms-4 ps-1">
          <a href="{{ route('dashboard.index', ['chart' => 'chart-donut']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-pie-chart-fill"></i><span>Distribuição Geral</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-evolution']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-graph-up-arrow"></i><span>Evolução Financeira</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-finance']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-bar-chart-fill"></i><span>Entradas x Despesas</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-expense-category']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-cash-stack"></i><span>Despesas por Categoria</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-income-category']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-wallet-fill"></i><span>Entradas por Categoria</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-aportes-vs-saidas']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-arrow-left-right"></i><span>Aportes vs Saídas</span>
          </a>
          <a href="{{ route('dashboard.index', ['chart' => 'chart-daily']) }}" class="text-white d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-calendar3"></i><span>Movimentações Diárias</span>
          </a>
        </div>
      </template>
    </div>
    <a href="{{ route('finance') }}" class="text-white d-flex align-items-center mb-3">
      <i class="bi bi-wallet2"></i>
      <template x-if="isExpanded"><span class="ms-2">Financeiro</span></template>
    </a>
    <a href="{{ route('credit_cards.index') }}" class="text-white d-flex align-items-center mb-3">
      <i class="bi bi-credit-card"></i>
      <template x-if="isExpanded"><span class="ms-2">Cartão</span></template>
    </a>
    <a href="{{ route('installments') }}" class="text-white d-flex align-items-center mb-3">
      <i class="bi bi-card-list"></i>
      <template x-if="isExpanded"><span class="ms-2">Parcelado</span></template>
    </a>
  </div>
  <div id="main-content" :class="isExpanded ? 'expanded-content' : 'collapsed-content'">
    <main>
      @yield('content')
    </main>
  </div>
  <div
    x-show="!isExpanded && openCharts"
    x-transition
    @click.outside="openCharts = false"
    @keydown.escape.window="openCharts = false"
    class="position-fixed  text-white shadow p-3 rounded bg-dark text-decoration-none"
    style="top: 140px; left: 80px; z-index: 1050; width: 230px; background-color: #343a40;">
    <div class="d-flex flex-column gap-2">
      <a href="{{ route('dashboard.index', ['chart' => 'chart-donut']) }}" class="text-white text-decoration-none">
        <i class="bi bi-pie-chart-fill me-2"></i> Distribuição Geral
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-evolution']) }}" class="text-white text-decoration-none">
        <i class="bi bi-graph-up-arrow me-2"></i> Evolução Financeira
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-finance']) }}" class="text-white text-decoration-none">
        <i class="bi bi-bar-chart-fill me-2"></i> Entradas x Despesas
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-expense-category']) }}" class="text-white text-decoration-none">
        <i class="bi bi-cash-stack me-2"></i> Despesas por Categoria
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-income-category']) }}" class="text-white text-decoration-none">
        <i class="bi bi-wallet-fill me-2"></i> Entradas por Categoria
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-aportes-vs-saidas']) }}" class="text-white text-decoration-none">
        <i class="bi bi-arrow-left-right me-2"></i> Aportes vs Saídas
      </a>
      <a href="{{ route('dashboard.index', ['chart' => 'chart-daily']) }}" class="text-white text-decoration-none">
        <i class="bi bi-calendar3 me-2"></i></i> Movimentações Diárias
      </a>
    </div>
  </div>
  @include('helpers._helper_modal')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
