<script>
  let categoryExpenseChart;

  function loadExpenseCategoriesChart(month) {
    $.ajax({
      url: '/dashboard/expenses-by-category',
      type: 'GET',
      data: { month },
      success: function (data) {
        const ctx = document.getElementById('expenseCategoryChart')?.getContext('2d');
        if (!ctx) return;

        if (categoryExpenseChart) categoryExpenseChart.destroy();

        categoryExpenseChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: Object.keys(data),
            datasets: [{
              label: 'R$ por categoria',
              data: Object.values(data),
              backgroundColor: '#dc3545'
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
        alert('Erro ao carregar gráfico de despesas por categoria.');
      }
    });
  }
</script>