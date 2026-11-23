                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="small">Total Employees</div>
                                        <strong class="fs-3">{{ number_format($dashboardStats['employees']) }}</strong>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('admin.employees.index') }}">View Employees</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-dark mb-4">
                                    <div class="card-body">
                                        <div class="small">Total User Accounts</div>
                                        <strong class="fs-3">{{ number_format($dashboardStats['users']) }}</strong>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-dark stretched-link" href="{{ route('admin.users.index') }}">View Users</a>
                                        <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <div class="small">Active Users</div>
                                        <strong class="fs-3">{{ number_format($dashboardStats['activeUsers']) }}</strong>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('admin.users.index', ['status' => 'active']) }}">View Active Users</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">
                                        <div class="small">Inactive Users</div>
                                        <strong class="fs-3">{{ number_format($dashboardStats['inactiveUsers']) }}</strong>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('admin.users.index', ['status' => 'inactive']) }}">View Inactive Users</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
