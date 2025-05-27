<script>
  $(document).ready(function () {
    let currentMonth = $('#dates').val() || new Date().getMonth() + 1;

    const chartRenders = {
      'chart-evolution': () => loadEvolutionChart(),
      'chart-donut': () => renderDonutChartFromServer(),
      'chart-finance': () => renderFinanceChartFromServer(),
      'chart-expense-category': () => loadExpenseCategoriesChart(currentMonth),
      'chart-income-category': () => loadIncomeCategoriesChart(currentMonth),
      'chart-aportes-vs-saidas': () => loadAportesVsSaidasChart(currentMonth),
      'chart-daily': () => loadDailyMovementsChart(currentMonth),
    };

    $('.chart-box').hide();

    const chartParam = new URLSearchParams(window.location.search).get('chart');
    const defaultChart = 'chart-donut';
    const selectedChart = chartParam && chartRenders[chartParam] ? chartParam : defaultChart;

    $('#' + selectedChart).fadeIn();
    chartRenders[selectedChart]();

    $(document).on('click', '.chart-link', function (e) {
      e.preventDefault();

      const target = $(this).data('chart');
      $('.chart-box').hide();
      $('#' + target).fadeIn();

      if (chartRenders[target]) {
        chartRenders[target]();
      }
    });

    $(document).on('change', '#dates', function () {
      currentMonth = $(this).val();
    });
  });
</script>