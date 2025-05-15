@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-dark fw-bold mb-0">📊 User Logs</h1>
        <div class="d-flex align-items-center">
            <form id="dateFilterForm" action="{{ route('admin.userLogs') }}" method="GET" class="d-flex align-items-center">
                <label for="date" class="me-2">Select Date:</label>
                <input type="date" name="date" id="date" value="{{ old('date', $selectedDate ?? $dateToday) }}" 
                       class="form-control" style="width: 200px;" />
            </form>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-success">
                    <tr>
                        <th>User</th>
                        <th>Event Type</th>
                        <th>Browser</th>
                        <th>Device</th>
                        <th>Platform</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($userLogs as $log)
                        <tr>
                            <td>
                                @if ($log->user)
                                    {{ $log->user->first_name }} {{ $log->user->last_name }}
                                @else
                                    <em class="text-muted">Unknown</em>
                                @endif
                            </td>
                            <td>{{ ucfirst($log->event_type) }}</td>
                            <td>{{ $log->browser ?? 'N/A' }}</td>
                            <td>{{ $log->device ?? 'N/A' }}</td>
                            <td>{{ $log->platform ?? 'N/A' }}</td>
                            <td>{{ $log->created_at ? $log->created_at->format('F j, Y') : 'N/A' }}</td>
                            <td>{{ $log->created_at ? $log->created_at->format('g:i A') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted fst-italic py-3">No logs found for the selected date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Submit form when date changes
        $('#date').on('change', function () {
            $('#dateFilterForm').submit();
        });
    });
</script>
@endpush
@endsection



