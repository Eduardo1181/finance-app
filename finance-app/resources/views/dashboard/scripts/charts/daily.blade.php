<script>
  let dailyMovementChart;

  function loadDailyMovementsChart(month) {
    $.ajax({
      url: '/dashboard/daily-movements',
      type: 'GET',
      data: { month },
      success: function (data) {
        const ctx = document.getElementById('dailyMovementsChart')?.getContext('2d');
        if (!ctx) return;

        if (dailyMovementChart) dailyMovementChart.destroy();

        dailyMovementChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: Object.keys(data),
            datasets: [{
              label: 'R$ por dia',
              data: Object.values(data),
              borderColor: '#0d6efd',
              tension: 0.3,
              fill: false
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      },
      error: function () {
        alert('Erro ao carregar movimentações diárias.');
      }
    });
  }

  let evolutionChart;

  function loadEvolutionChart(months = 6) {
    $.ajax({
      url: '/dashboard/balance-history',
      method: 'GET',
      data: { months: months },
      dataType: 'json',
      success: function (data) {
        const ctx = document.getElementById('evolutionChart')?.getContext('2d');
        if (!ctx) return;

        const labels = data.map(item => item.label);
        const values = data.map(item => item.value);

        if (evolutionChart) evolutionChart.destroy();

        evolutionChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Saldo Mensal (R$)',
              data: values,
              fill: true,
              tension: 0.4,
              borderWidth: 2,
              pointRadius: 4,
              borderColor: 'rgba(13, 110, 253, 1)',
              backgroundColor: 'rgba(13, 110, 253, 0.2)'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'top' }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: value => 'R$ ' + value.toFixed(2).replace('.', ',')
                }
              }
            }
          }
        });
      },
      error: function (xhr, status, error) {
        console.error('Erro ao carregar evolução financeira:', error);
      }
    });
  }
</script>
