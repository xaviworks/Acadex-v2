@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">User Logs</h1>
    <p class="mb-6 text-gray-600">A record of user login, logout, or login attempts.</p>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form id="dateFilterForm" action="{{ route('admin.userLogs') }}" method="GET" class="flex items-center space-x-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Select Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', $selectedDate ?? $dateToday) }}" 
                       class="mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64" />
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="userLogsTable" class="min-w-full text-sm">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Event Type</th>
                    <th class="px-4 py-3 text-left">Browser</th>
                    <th class="px-4 py-3 text-left">Device</th>
                    <th class="px-4 py-3 text-left">Platform</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Time</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="logTableBody">
                @forelse ($userLogs as $log)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if ($log->user)
                                {{ $log->user->first_name }} {{ $log->user->last_name }}
                            @else
                                <em class="text-gray-400 italic">Unknown</em>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ ucfirst($log->event_type) }}</td>
                        <td class="px-4 py-3">{{ $log->browser ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $log->device ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $log->platform ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $log->created_at ? $log->created_at->format('F j, Y') : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $log->created_at ? $log->created_at->format('g:i A') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                        <td class="px-4 py-3">Empty</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#userLogsTable').DataTable({
            ordering: false,
            paging: true,
            responsive: true, // Optional, improves display on small screens
        });

        // Submit form when date changes
        $('#date').on('change', function () {
            $('#dateFilterForm').submit();
        });
    });
</script>
@endpush



