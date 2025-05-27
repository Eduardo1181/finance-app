<script>
  if (typeof financeChart === 'undefined') {
    var financeChart;
  }

  function renderFinanceChartFromServer() {
    const month = $('#dates').val() || new Date().getMonth() + 1;

    $.get('/dashboard/monthly-data', { month }, function (data) {
      renderFinanceChart(data.totalIncomes, data.totalExpenses);
    });
  }

  function parseValor(valor) {
    return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0;
  }

  function renderFinanceChart(incomes, expenses) {
    const ctx = document.getElementById('financeChart')?.getContext('2d');
    if (!ctx) return;

    if (financeChart) financeChart.destroy();

    financeChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Entradas', 'Despesas'],
        datasets: [{
          label: 'R$',
          data: [
            parseValor(incomes),
            parseValor(expenses)
          ],
          backgroundColor: ['#198754', '#dc3545']
        }]
      },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
      }
    });
  }
</script>