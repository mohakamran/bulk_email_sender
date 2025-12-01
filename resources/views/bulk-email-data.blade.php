@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Bulk Email Sender</h5>
                    <p class="text-muted">Send personalized emails to multiple recipients</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('bulk-email.index') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Emails
                        </a>
                        <a href="{{ route('bulk-email.data') }}" class="btn btn-outline-primary">
                            <i class="fas fa-database me-2"></i>View Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-database me-2"></i>
                        Email Job Records
                    </h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search and Filter -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('bulk-email.data') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                        placeholder="Search by name, email, or research..." 
                                        value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('bulk-email.data') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <div class="input-group">
                                    <select class="form-select" name="status" onchange="this.form.submit()">
                                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>
                                            All Status
                                        </option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>
                                            Sent
                                        </option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                                            Failed
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>University</th>
                                    <th>Research Field</th>
                                    <th>Status</th>
                                    <th>Scheduled At</th>
                                    <th>Created At</th>
                                    <th>CV</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emailJobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>{{ $job->name }}</td>
                                        <td>{{ $job->email }}</td>
                                        <td>{{ $job->university }}</td>
                                        <td>{{ $job->research }}</td>
                                        <td>
                                            <span class="badge bg-{{ $job->status == 'pending' ? 'warning' : ($job->status == 'sent' ? 'success' : 'danger') }}">
                                                {{ ucfirst($job->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $job->scheduled_at ? $job->scheduled_at->format('M j, Y g:i A') : 'Not scheduled' }}
                                        </td>
                                        <td>{{ $job->created_at->format('M j, Y g:i A') }}</td>
                                        <td>
                                            @if($job->cv_path)
                                                <a href="{{ Storage::url($job->cv_path) }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-pdf"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($job->status != 'sent')
                                                <form method="POST" action="{{ route('bulk-email.send.individual', $job->id) }}" 
                                                      style="display: inline;" onsubmit="return confirm('Send email to {{ $job->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link text-success p-0 text-decoration-none me-2" 
                                                            style="font-weight: 500;">
                                                        <i class="fas fa-paper-plane me-1"></i>Send
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success me-2">Sent</span>
                                                <form method="POST" action="{{ route('bulk-email.send.individual', $job->id) }}" 
                                                      style="display: inline;" onsubmit="return confirm('Send email again to {{ $job->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link text-primary p-0 text-decoration-none" 
                                                            style="font-weight: 500;">
                                                        <i class="fas fa-redo me-1"></i>Send Again
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No records found. Start by adding some email jobs!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $emailJobs->firstItem() }} to {{ $emailJobs->lastItem() }} 
                            of {{ $emailJobs->total() }} records
                        </div>
                        <div>
                            {{ $emailJobs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endsection
