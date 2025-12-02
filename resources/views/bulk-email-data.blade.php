@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Bulk Email Jobs</h3>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('bulk-email.data') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search name, email, university, research..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('bulk-email.data') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>University</th>
                            <th>Research</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emailJobs as $job)
                            <tr>
                                <td>{{ $loop->iteration + ($emailJobs->currentPage() - 1) * $emailJobs->perPage() }}</td>
                                <td>{{ $job->name }}</td>
                                <td>{{ $job->email }}</td>
                                <td>{{ $job->university }}</td>
                                <td>{{ $job->research }}</td>
                                <td>
                                    <span class="badge bg-{{ $job->status === 'sent' ? 'success' : ($job->status === 'failed' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td>{{ $job->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $emailJobs->firstItem() ?? 0 }} - {{ $emailJobs->lastItem() ?? 0 }} of {{ $emailJobs->total() }} results
                </div>
                <div>
                    {{ $emailJobs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Make pagination arrows smaller and bootstrap-like */
.pagination svg {
    width: 14px !important;
    height: 14px !important;
}
.pagination .page-link {
    padding: 6px 10px !important;
    font-size: 14px !important;
}

/* Hide pagination arrows */
.pagination .page-link svg {
    display: none !important;
}

/* Optional: replace with text arrows if you want */
.pagination .page-item:first-child .page-link::before {
    content: "«";
}
.pagination .page-item:last-child .page-link::after {
    content: "»";
}

</style>
@endpush
