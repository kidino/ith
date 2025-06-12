<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{ $tickets->links() }}


                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assignees</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($tickets as $ticket)
                                <tr class="@if($loop->even) bg-gray-50 @endif">
                                    <td class="px-4 py-2">{{ $ticket->title }}</td>
                                    <td class="px-4 py-2">{{ $ticket->status?->name }}</td>
                                    <td class="px-4 py-2">{{ $ticket->category?->name }}</td>
                                    <td class="px-4 py-2">{{ $ticket->user->department?->name }}</td>
                                    <td class="px-4 py-2">
                                        @foreach($ticket->assignees as $assignee)
                                            <span class="inline-block bg-gray-200 rounded px-2 py-1 text-xs text-gray-700 mr-1">{{ $assignee->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-sm">View</a>
                                        <a href="{{ route('tickets.edit', $ticket) }}" class="text-yellow-600 hover:underline text-sm ml-2">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-2 text-center text-gray-400">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
