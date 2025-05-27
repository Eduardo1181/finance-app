<script>
  let categoryIncomeChart;

  function loadIncomeCategoriesChart(month) {
    $.ajax({
      url: '/dashboard/incomes-by-category',
      type: 'GET',
      data: { month },
      success: function (data) {
        const ctx = document.getElementById('incomeCategoryChart')?.getContext('2d');
        if (!ctx) return;

        if (categoryIncomeChart) categoryIncomeChart.destroy();

        categoryIncomeChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: Object.keys(data),
            datasets: [{
              label: 'R$ por categoria',
              data: Object.values(data),
              backgroundColor: '#198754'
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
        alert('Erro ao carregar gráfico de entradas por categoria.');
      }
    });
  }
</script>
