<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<script src="{{ asset('backend/dist/js/scripts.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

@if (request()->routeIs('charts') || (request()->routeIs('dashboard') && auth()->user()->isAdmin()))
<script src="{{ asset('backend/dist/assets/demo/chart-area-demo.js') }}?v={{ filemtime(public_path('backend/dist/assets/demo/chart-area-demo.js')) }}"></script>

<script src="{{ asset('backend/dist/assets/demo/chart-bar-demo.js') }}?v={{ filemtime(public_path('backend/dist/assets/demo/chart-bar-demo.js')) }}"></script>

<script src="{{ asset('backend/dist/assets/demo/chart-pie-demo.js') }}?v={{ filemtime(public_path('backend/dist/assets/demo/chart-pie-demo.js')) }}"></script>

<script>
    (() => {
        const modalElement = document.getElementById('chartViewModal');
        const modalCanvas = document.getElementById('chartViewModalCanvas');
        const modalTitle = document.getElementById('chartViewModalTitle');
        const periodControl = modalElement?.querySelector('[data-chart-period-control]');
        const periodFilter = modalElement?.querySelector('[data-chart-period-filter]');

        if (!modalElement || !modalCanvas || !window.Chart) {
            return;
        }

        let expandedChart = null;
        let activeChartId = null;
        let activeChartDefinition = null;
        const chartModal = new bootstrap.Modal(modalElement);

        const updateEmployeeHirePeriod = () => {
            if (activeChartId !== 'myAreaChart' || !expandedChart || !periodFilter) {
                return;
            }

            const periods = {
                all: { data: window.appChartData.hiresByYear, label: 'All Time' },
                fiveYears: { data: window.appChartData.hiresByFiveYears, label: 'Last 5 Years' },
                twelveMonths: { data: window.appChartData.hiresByMonth, label: 'Last 12 Months' },
            };
            const period = periods[periodFilter.value] || periods.all;
            expandedChart.data.labels = period.data.labels;
            expandedChart.data.datasets[0].data = period.data.values;
            expandedChart.update();
            modalTitle.textContent = `Employee Hires (${period.label})`;
        };

        modalElement.addEventListener('shown.bs.modal', () => {
            expandedChart?.destroy();
            expandedChart = new Chart(modalCanvas.getContext('2d'), {
                type: activeChartDefinition.type,
                data: JSON.parse(JSON.stringify(activeChartDefinition.data)),
                options: Object.assign({}, activeChartDefinition.options, {
                    responsive: true,
                    maintainAspectRatio: false,
                }),
            });

            updateEmployeeHirePeriod();
        });

        periodFilter?.addEventListener('change', updateEmployeeHirePeriod);

        document.querySelectorAll('[data-chart-view]').forEach((button) => {
            button.addEventListener('click', () => {
                activeChartId = button.dataset.chartId;
                activeChartDefinition = window.appChartDefinitions?.[activeChartId];

                if (!activeChartDefinition) {
                    return;
                }

                const isEmployeeHireChart = activeChartId === 'myAreaChart';
                periodControl?.classList.toggle('d-none', !isEmployeeHireChart);
                if (periodFilter) {
                    periodFilter.value = 'all';
                }
                modalTitle.textContent = button.dataset.chartTitle || 'Chart';
                chartModal.show();
            });
        });

        modalElement.addEventListener('hidden.bs.modal', () => {
            expandedChart?.destroy();
            expandedChart = null;
        });
    })();
</script>
@endif

@if (request()->routeIs('tables'))
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>

<script src="{{ asset('backend/dist/js/datatables-simple-demo.js') }}?v={{ filemtime(public_path('backend/dist/js/datatables-simple-demo.js')) }}"></script>
@endif

@if (request()->routeIs('admin.users.index', 'admin.employees.index'))
<script src="{{ asset('backend/dist/js/user-management.js') }}?v={{ filemtime(public_path('backend/dist/js/user-management.js')) }}"></script>
@endif
