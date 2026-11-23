<div class="modal fade" id="chartViewModal" tabindex="-1" aria-labelledby="chartViewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="chartViewModalTitle">Chart</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close chart"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-3 d-none" data-chart-period-control>
                    <label class="visually-hidden" for="chartViewPeriod">Hire date range</label>
                    <select class="form-select form-select-sm w-auto" id="chartViewPeriod" data-chart-period-filter aria-label="Filter employee hires by date range">
                        <option value="all">All time</option>
                        <option value="fiveYears">Last 5 years</option>
                        <option value="twelveMonths">Last 12 months</option>
                    </select>
                </div>
                <div class="chart-modal-canvas-wrap">
                    <canvas id="chartViewModalCanvas" aria-label="Expanded chart" role="img"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
