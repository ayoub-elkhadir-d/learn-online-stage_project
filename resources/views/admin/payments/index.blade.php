@extends('admin.layout')

@section('title', 'Payments')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Payment Requests</h5>
        <div>
            <span class="badge bg-warning text-dark me-2 px-3 py-2">
                <i class="fas fa-clock me-1"></i>
                Pending: {{ $payments->total() - $payments->getCollection()->where('status','paid')->count() }}
            </span>
            <span class="badge bg-success px-3 py-2">
                <i class="fas fa-check me-1"></i>
                Approved: {{ $payments->getCollection()->where('status','paid')->count() }}
            </span>
        </div>
    </div>

    @if($payments->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#a78bfa;"></i>
            <h6 class="fw-bold text-dark">No payment requests yet</h6>
            <p class="text-muted">Payment requests from users will appear here.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Full Name / RIB</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Reference</th>
                        <th>Receipt</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end" style="min-width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:#ede9fe;color:#6d28d9;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13px;font-weight:600;">{{ $payment->user->name }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">{{ $payment->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:500;">{{ $payment->full_name ?? $payment->user->name }}</div>
                            <div style="font-size:11px;font-family:monospace;color:#6d28d9;background:#ede9fe;display:inline-block;padding:1px 6px;border-radius:4px;">
                                RIB: {{ $payment->rib ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:500;">{{ $payment->course->title }}</div>
                            <div style="font-size:11px;color:#9ca3af;">{{ $payment->course->category->name }}</div>
                        </td>
                        <td style="font-weight:600;font-size:14px;">{{ number_format($payment->course->price_mad, 0, ',', ' ') }} MAD</td>
                        <td>
                            @if($payment->reference)
                                <span style="font-size:12px;background:#f3f4f6;padding:3px 8px;border-radius:6px;font-family:monospace;">
                                    {{ $payment->reference }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->receipt_path)
                                <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="btn btn-sm px-2" style="background:#ede9fe;color:#6d28d9;border-radius:6px;font-size:11px;text-decoration:none;">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            @else
                                <span class="text-muted" style="font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#6b7280;">
                            {{ $payment->purchased_at ? $payment->purchased_at->format('d M Y, H:i') : 'N/A' }}
                        </td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge bg-success px-3 py-2" style="font-size:11px;">
                                    <i class="fas fa-check me-1"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2" style="font-size:11px;">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($payment->status !== 'paid')
                                <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-success btn-sm px-3" style="border-radius:8px;" title="Approve Payment">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm px-3" style="border-radius:8px;" title="Reject Payment" onclick="return confirm('Reject this payment request? The record will be removed.')">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </form>
                            @else
                                <span style="font-size:12px;color:#9ca3af;">
                                    <i class="fas fa-check-circle me-1" style="color:#10b981;"></i> Completed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection