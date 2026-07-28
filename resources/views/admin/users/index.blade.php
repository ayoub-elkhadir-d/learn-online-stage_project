@extends('admin.layout')

@section('title', 'Users')

@section('content')
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="card p-3 text-center">
            <div style="font-size:1.8rem;font-weight:700;color:#4f46e5;">{{ $stats['total'] }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Total Users</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card p-3 text-center">
            <div style="font-size:1.8rem;font-weight:700;color:#10b981;">{{ $stats['active'] }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Active</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card p-3 text-center">
            <div style="font-size:1.8rem;font-weight:700;color:#ef4444;">{{ $stats['banned'] }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Banned</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card p-3 text-center">
            <div style="font-size:1.8rem;font-weight:700;color:#f59e0b;">{{ $stats['with_courses'] }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">With Purchased Courses</div>
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">All Users</h5>
        <span class="badge bg-secondary px-3 py-2" style="font-size:11px;">
            <i class="fas fa-users me-1"></i> {{ $users->total() }} users
        </span>
    </div>

    @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x mb-3 d-block" style="color:#a78bfa;"></i>
            <h6 class="fw-bold text-dark">No users found</h6>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Joined</th>
                        <th>Courses Purchased</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;background:#ede9fe;color:#6d28d9;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;">{{ $user->name }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;color:#6b7280;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge" style="background:#ede9fe;color:#6d28d9;font-size:11px;font-weight:600;">
                                <i class="fas fa-book-open me-1"></i> {{ $user->purchases_count }} courses
                            </span>
                        </td>
                        <td>
                            @if($user->isBanned())
                                <span class="badge bg-danger px-3 py-2" style="font-size:11px;">
                                    <i class="fas fa-ban me-1"></i> Banned
                                </span>
                            @else
                                <span class="badge bg-success px-3 py-2" style="font-size:11px;">
                                    <i class="fas fa-check me-1"></i> Active
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($user->isBanned())
                                <form method="POST" action="{{ route('admin.users.unban', $user) }}" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-success btn-sm px-3" style="border-radius:8px;" title="Unban User">
                                        <i class="fas fa-unlock me-1"></i> Unban
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.ban', $user) }}" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius:8px;" title="Ban User" onclick="return confirm('Ban user {{ $user->name }}? They will lose access.')">
                                        <i class="fas fa-ban me-1"></i> Ban
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection