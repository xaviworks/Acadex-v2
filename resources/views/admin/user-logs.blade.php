@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">User Logs</h1>
    <p class="mb-6 text-gray-600">A record of user login, logout, or login attempts.</p>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form action="#" method="GET" class="flex items-center space-x-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Select Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', $dateToday) }}" 
                       class="mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64" />
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table id="userLogsTable" class="min-w-full table-auto text-sm">
            <thead class="bg-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Event Type</th>
                    <th class="px-6 py-3 text-left">Browser</th>
                    <th class="px-6 py-3 text-left">Device</th>
                    <th class="px-6 py-3 text-left">Platform</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Time</th>
                </tr>
            </thead>
            <tbody class="text-gray-700" id="logTableBody">
                @foreach ($userLogs as $log)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                        <td class="px-6 py-4">{{ ucfirst($log->event_type) }}</td>
                        <td class="px-6 py-4">{{ $log->browser }}</td>
                        <td class="px-6 py-4">{{ $log->device }}</td>
                        <td class="px-6 py-4">{{ $log->platform }}</td>
                        <td class="px-6 py-4">{{ $log->created_at->format('F j, Y') }}</td>
                        <td class="px-6 py-4">{{ $log->created_at->format('g:iA') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Listen for date change event
        $('#date').on('change', function () {
            const selectedDate = $(this).val();
            if (!selectedDate) return;

            $.ajax({
                url: "{{ route('admin.user_logs.filter') }}",  // Route to the filter action
                method: 'GET',
                data: {
                    date: selectedDate
                },
                success: function (response) {
                    // Replace table body content with the new filtered rows
                    $('#logTableBody').html(response);
                },
                error: function () {
                    alert('Failed to filter logs. Please try again.');
                }
            });
        });
    });

        $(document).ready(function() {
            // Initialize DataTable globally if any table needs DataTable
            $('#userLogsTable').DataTable();
        });
</script>
@endpush
