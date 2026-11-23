(() => {
  if (!window.Chart) return;
  Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#292b2c';
  window.appChartDefinitions = window.appChartDefinitions || {};
  window.appCharts = window.appCharts || {};

  const chartData = window.appChartData?.hiresByYear;
  const canvas = document.getElementById('myAreaChart');
  if (!canvas || !chartData) return;

  if (chartData.labels.length === 0) {
    const emptyState = document.createElement('div');
    emptyState.className = 'chart-empty';
    emptyState.textContent = 'No employee hire dates are available.';
    canvas.replaceWith(emptyState);
    canvas.closest('.chart-card')?.querySelector('[data-chart-view]')?.setAttribute('disabled', 'disabled');
    return;
  }

  const definition = {
    type: 'line',
    data: { labels: chartData.labels, datasets: [{ label: 'Employees hired', lineTension: 0.3, backgroundColor: 'rgba(2,117,216,0.18)', borderColor: 'rgba(2,117,216,1)', pointRadius: 4, pointBackgroundColor: 'rgba(2,117,216,1)', pointBorderColor: '#fff', pointBorderWidth: 2, data: chartData.values }] },
    options: { responsive: true, maintainAspectRatio: false, scales: { xAxes: [{ gridLines: { display: false }, ticks: { maxTicksLimit: 8 } }], yAxes: [{ ticks: { beginAtZero: true, precision: 0, maxTicksLimit: 5 }, gridLines: { color: 'rgba(0,0,0,.125)' } }] }, legend: { display: false } },
  };
  window.appChartDefinitions.myAreaChart = definition;
  window.appCharts.myAreaChart = new Chart(canvas.getContext('2d'), { type: definition.type, data: JSON.parse(JSON.stringify(definition.data)), options: definition.options });
})();
