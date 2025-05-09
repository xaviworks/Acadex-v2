@forelse ($userLogs as $log)
    <tr class="border-b hover:bg-gray-50">
        <td class="px-6 py-4">{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
        <td class="px-6 py-4">{{ ucfirst($log->event_type) }}</td>
        <td class="px-6 py-4">{{ $log->browser }}</td>
        <td class="px-6 py-4">{{ $log->device }}</td>
        <td class="px-6 py-4">{{ $log->platform }}</td>
        <td class="px-6 py-4">{{ $log->created_at->format('F j, Y') }}</td>
        <td class="px-6 py-4">{{ $log->created_at->format('g:i A') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-gray-500 px-6 py-4">
            No logs found for the selected date.
        </td>
    </tr>
@endforelse
