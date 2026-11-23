<!DOCTYPE html>
<html lang="en">
    @section('title', 'Charts')
    @include('partials.header')

    <body class="sb-nav-fixed">
        @include('partials.navbar')
        <div id="layoutSidenav">
            @include('partials.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Charts</h1>
                        <ol class="breadcrumb mb-4"><li class="breadcrumb-item active">Charts</li></ol>
                        <div class="chart-grid mb-4">
                            <article class="card chart-card">
                                <div class="card-header">
                                    <span class="chart-card-title"><i class="fas fa-chart-area" aria-hidden="true"></i> Employee Hires (All Time)</span>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-chart-view data-chart-id="myAreaChart" data-chart-title="Employee Hires (All Time)">
                                        <i class="fas fa-up-right-and-down-left-from-center me-1" aria-hidden="true"></i> View
                                    </button>
                                </div>
                                <div class="card-body"><div class="chart-canvas-wrap"><canvas id="myAreaChart" aria-label="Area chart" role="img"></canvas></div></div>
                            </article>
                            <article class="card chart-card">
                                <div class="card-header">
                                    <span class="chart-card-title"><i class="fas fa-chart-bar" aria-hidden="true"></i> Employees by Office</span>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-chart-view data-chart-id="myBarChart" data-chart-title="Employees by Office">
                                        <i class="fas fa-up-right-and-down-left-from-center me-1" aria-hidden="true"></i> View
                                    </button>
                                </div>
                                <div class="card-body"><div class="chart-canvas-wrap"><canvas id="myBarChart" aria-label="Bar chart" role="img"></canvas></div></div>
                            </article>
                            <article class="card chart-card">
                                <div class="card-header">
                                    <span class="chart-card-title"><i class="fas fa-chart-pie" aria-hidden="true"></i> User Accounts by Status</span>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-chart-view data-chart-id="myPieChart" data-chart-title="User Accounts by Status">
                                        <i class="fas fa-up-right-and-down-left-from-center me-1" aria-hidden="true"></i> View
                                    </button>
                                </div>
                                <div class="card-body"><div class="chart-canvas-wrap"><canvas id="myPieChart" aria-label="Pie chart" role="img"></canvas></div></div>
                            </article>
                        </div>
                        @include('partials.chart-data')
                        @include('partials.chart-view-modal')
                    </div>
                </main>
                @include('partials.footer')
            </div>
        </div>
        @include('partials.scripts')
    </body>
</html>
