<!DOCTYPE html>
<html lang="en">
    @section('title', 'User Profile')
    @include('partials.header')

    <body class="sb-nav-fixed">
        @include('partials.navbar')
        <div id="layoutSidenav">
            @include('partials.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3">
                            <div>
                                <h1>User Profile</h1>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                                    <li class="breadcrumb-item active">{{ $user->full_name ?: $user->name }}</li>
                                </ol>
                            </div>
                            <a class="btn btn-outline-secondary mt-2" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-arrow-left me-1"></i> Back to Users
                            </a>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card mb-4">
                                    <div class="card-header"><i class="fas fa-user me-1"></i> Account Details</div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4">Full Name</dt>
                                            <dd class="col-sm-8">{{ $user->full_name ?: $user->name ?: 'Not provided' }}</dd>

                                            <dt class="col-sm-4">Username</dt>
                                            <dd class="col-sm-8">{{ $user->username ?: 'Not provided' }}</dd>

                                            <dt class="col-sm-4">Email Address</dt>
                                            <dd class="col-sm-8">{{ $user->email }}</dd>

                                            <dt class="col-sm-4">Role</dt>
                                            <dd class="col-sm-8">{{ $role ?: 'Not assigned' }}</dd>

                                            <dt class="col-sm-4">Account Status</dt>
                                            <dd class="col-sm-8"><span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">{{ $user->status ? 'Active' : 'Inactive' }}</span></dd>

                                            <dt class="col-sm-4">Created</dt>
                                            <dd class="col-sm-8">{{ $user->created_at?->format('M j, Y g:i A') ?? 'Not available' }}</dd>

                                            <dt class="col-sm-4">Last Updated</dt>
                                            <dd class="col-sm-8">{{ $user->updated_at?->format('M j, Y g:i A') ?? 'Not available' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-primary mb-4">
                                    <div class="card-header"><i class="fas fa-id-card me-1"></i> Profile Overview</div>
                                    <div class="card-body text-center">
                                        @if ($user->profile_picture_url)
                                            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->full_name ?: $user->name }} profile picture" class="rounded-circle mb-3" width="96" height="96" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                                                <i class="fas fa-user fa-2x" aria-hidden="true"></i>
                                            </div>
                                        @endif
                                        <h2 class="h5 mb-1">{{ $user->full_name ?: $user->name ?: 'Unnamed User' }}</h2>
                                        <p class="text-muted mb-3">{{ $user->username ? '@'.$user->username : 'No username' }}</p>
                                        <div class="border-top pt-3 text-start">
                                            <div class="small text-muted">User ID</div>
                                            <div class="fw-semibold">#{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                @include('partials.footer')
            </div>
        </div>
        @include('partials.scripts')
    </body>
</html>
