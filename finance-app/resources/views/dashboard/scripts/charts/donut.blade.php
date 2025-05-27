<script>
  let donutChart;

  function renderDonutChartFromServer() {
    $.get('/dashboard/monthly-data', { month: $('#dates').val() || new Date().getMonth() + 1 }, function (data) {
      renderDonutChart(data.totalIncomes, data.totalExpenses, data.totalDeposits, data.totalWithdrawals);
    });
  }

  function parseValor(valor) {
  return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0;
  }

  function renderDonutChart(incomes, expenses, deposits, withdrawals) {
    const ctx = document.getElementById('donutChart')?.getContext('2d');
    if (!ctx) return;

    if (donutChart) donutChart.destroy();

    donutChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Entradas', 'Despesas', 'Aportes', 'Saídas'],
        datasets: [{
          label: 'R$',
          data: [
            parseValor(incomes),
            parseValor(expenses),
            parseValor(deposits),
            parseValor(withdrawals)
          ],
          backgroundColor: ['#198754', '#dc3545', '#0d6efd', '#b21613'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }
</script>
