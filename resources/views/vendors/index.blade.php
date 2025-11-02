<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vendors') }}
            </h2>
            <a href="{{ route('vendors.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow-sm hover:bg-blue-700 text-sm font-semibold">
                + Add Vendor
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                @if($vendors->hasPages())
                <div class="px-6 py-6 text-gray-900 pagination">
                        {{ $vendors->links() }}
                </div>
                @endif

                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($vendors as $vendor)
                            <tr>
                                <td class="px-4 py-2">{{ $vendor->id }}</td>
                                <td class="px-4 py-2">{{ $vendor->code }}</td>
                                <td class="px-4 py-2">{{ $vendor->name }}</td>
                                <td class="px-4 py-2">
                                    <div class="text-xs">
                                        @if($vendor->person_in_charge)
                                            <div class="font-semibold">{{ $vendor->person_in_charge }}</div>
                                        @endif
                                        @if($vendor->email)
                                            <div>{{ $vendor->email }}</div>
                                        @endif
                                        @if($vendor->phone_number)
                                            <div>{{ $vendor->phone_number }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('vendors.show', $vendor) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                    <a href="{{ route('vendors.edit', $vendor) }}" class="text-yellow-600 hover:underline text-xs ml-2">Edit</a>
                                    <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this vendor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-400">No vendors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>