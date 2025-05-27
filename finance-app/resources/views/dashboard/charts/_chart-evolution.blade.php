<div class="mt-1 card chart-box" id="chart-evolution" style="display: none;">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="card-title">Evolução Financeira</h5>
      <div>
        <button class="btn btn-outline-primary btn-sm" onclick="loadEvolutionChart(6)">Últimos 6 meses</button>
        <button class="btn btn-outline-primary btn-sm" onclick="loadEvolutionChart(12)">Últimos 12 meses</button>
      </div>
    </div>
    <div style="max-width: 700px; margin: 0 auto;">
      <canvas id="evolutionChart"></canvas>
    </div>
  </div>
</div>
