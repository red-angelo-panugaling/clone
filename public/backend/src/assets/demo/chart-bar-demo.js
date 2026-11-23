(() => {
  if (!window.Chart) return;
  Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#292b2c';
  window.appChartDefinitions = window.appChartDefinitions || {};
  window.appCharts = window.appCharts || {};
  const chartData = window.appChartData?.employeesByOffice;
  const canvas = document.getElementById('myBarChart');
  if (!canvas || !chartData) return;
  if (chartData.labels.length === 0) {
    const emptyState = document.createElement('div');
    emptyState.className = 'chart-empty';
    emptyState.textContent = 'No active employee records to display.';
    canvas.replaceWith(emptyState);
    canvas.closest('.chart-card')?.querySelector('[data-chart-view]')?.setAttribute('disabled', 'disabled');
    return;
  }
  const definition = {
    type: 'bar',
    data: { labels: chartData.labels, datasets: [{ label: 'Employees', backgroundColor: 'rgba(2,117,216,1)', borderColor: 'rgba(2,117,216,1)', data: chartData.values }] },
    options: { responsive: true, maintainAspectRatio: false, scales: { xAxes: [{ gridLines: { display: false }, ticks: { maxRotation: 45, minRotation: 0 } }], yAxes: [{ ticks: { beginAtZero: true, precision: 0, maxTicksLimit: 5 }, gridLines: { display: true } }] }, legend: { display: false } },
  };
  window.appChartDefinitions.myBarChart = definition;
  window.appCharts.myBarChart = new Chart(canvas.getContext('2d'), { type: definition.type, data: JSON.parse(JSON.stringify(definition.data)), options: definition.options });
})();
