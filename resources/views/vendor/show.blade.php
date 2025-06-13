{{-- filepath: c:\laragon\www\ith\resources\views\vendor\show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vendor Detail') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-2">Vendor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="font-semibold">Code:</div>
                            <div>{{ $vendor->code }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Name:</div>
                            <div>{{ $vendor->name }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Phone Number:</div>
                            <div>{{ $vendor->phone_number }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Email:</div>
                            <div>{{ $vendor->email }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Person In Charge:</div>
                            <div>{{ $vendor->person_in_charge }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="font-semibold">Address:</div>
                            <div>{{ $vendor->address }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-2">Users</h3>
                    <table class="w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($vendor->users as $user)
                                <tr>
                                    <td class="px-4 py-2">{{ $user->id }}</td>
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-400">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    <a href="{{ route('vendors.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm">Back</a>
                    <a href="{{ route('vendors.edit', $vendor) }}" class="ml-2 px-4 py-2 bg-yellow-500 text-white rounded text-sm">Edit</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>