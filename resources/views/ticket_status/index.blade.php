{{-- filepath: c:\laragon\www\ith\resources\views\ticket_status\index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket Statuses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">


            
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Statuses</h3>
                    <a href="{{ route('ticket-statuses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Add Status</a>
                </div>

                <div class="mb-4 pagination">
                    {{ $statuses->links() }}
                </div>

                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($statuses as $status)
                            <tr>
                                <td class="px-4 py-2">{{ $status->id }}</td>
                                <td class="px-4 py-2">{{ $status->name }}</td>
                                <td class="px-4 py-2">
                                    @if($status->color)
                                        <span class="inline-block px-2 py-1 rounded text-xs" style="background: {{ $status->color }}; color: #fff;">
                                            {{ $status->color }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($status->default_status)
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800 font-semibold">Default</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('ticket-statuses.show', $status) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                    <a href="{{ route('ticket-statuses.edit', $status) }}" class="text-yellow-600 hover:underline text-xs ml-2">Edit</a>
                                    <form action="{{ route('ticket-statuses.destroy', $status) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this status?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-400">No statuses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


            </div>
            </div>
        </div>
    </div>
</x-app-layout>