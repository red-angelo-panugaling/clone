(() => {
  if (!window.Chart) return;
  Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#292b2c';
  window.appChartDefinitions = window.appChartDefinitions || {};
  window.appCharts = window.appCharts || {};
  const chartData = window.appChartData?.userAccountsByStatus;
  const canvas = document.getElementById('myPieChart');
  if (!canvas || !chartData) return;
  if (chartData.values.reduce((total, value) => total + value, 0) === 0) {
    const emptyState = document.createElement('div');
    emptyState.className = 'chart-empty';
    emptyState.textContent = 'No user accounts to display.';
    canvas.replaceWith(emptyState);
    canvas.closest('.chart-card')?.querySelector('[data-chart-view]')?.setAttribute('disabled', 'disabled');
    return;
  }
  const definition = {
    type: 'pie',
    data: { labels: chartData.labels, datasets: [{ data: chartData.values, backgroundColor: ['#28a745', '#dc3545'] }] },
    options: { responsive: true, maintainAspectRatio: false, legend: { position: 'top' }, tooltips: { callbacks: { label: (tooltipItem, data) => `${data.labels[tooltipItem.index] || ''}: ${data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]}` } } },
  };
  window.appChartDefinitions.myPieChart = definition;
  window.appCharts.myPieChart = new Chart(canvas.getContext('2d'), { type: definition.type, data: JSON.parse(JSON.stringify(definition.data)), options: definition.options });
})();
