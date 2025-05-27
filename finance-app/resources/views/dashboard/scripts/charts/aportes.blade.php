<script>
  if (typeof aportesVsSaidasChart === 'undefined') {
    var aportesVsSaidasChart;
  }

  function loadAportesVsSaidasChart(month) {
    $.ajax({
      url: '/dashboard/aportes-vs-saidas',
      type: 'GET',
      data: { month },
      success: function (data) {
        const ctx = document.getElementById('aportesVsSaidasChart')?.getContext('2d');
        if (!ctx) return;

        if (aportesVsSaidasChart) aportesVsSaidasChart.destroy();

        aportesVsSaidasChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: Object.keys(data),
            datasets: [{
              label: 'R$',
              data: Object.values(data),
              backgroundColor: ['#0d6efd', '#dc3545']
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      },
      error: function () {
        alert('Erro ao carregar gráfico de Aportes vs Saídas.');
      }
    });
  }
</script>
