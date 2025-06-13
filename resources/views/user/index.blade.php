{{-- filepath: c:\laragon\www\ith\resources\views\user\index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                {{-- Tabs for user_type --}}
                @php
                    $userTypeTab = request('type', 'all');
                    $tabClasses = function($active) {
                        return $active
                            ? 'inline-block px-4 py-2 text-sm font-semibold text-blue-700 bg-blue-50 border-b-2 border-blue-600 rounded-t transition'
                            : 'inline-block px-4 py-2 text-sm font-medium text-gray-500 hover:text-blue-700 hover:bg-gray-50 border-b-2 border-transparent rounded-t transition';
                    };
                    $tabTypes = [
                        'all' => 'All',
                        'admin' => 'Admin',
                        'it' => 'IT',
                        'user' => 'User',
                        'vendor' => 'Vendor',
                    ];
                @endphp
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex space-x-2" aria-label="Tabs">
                        @foreach($tabTypes as $typeKey => $typeLabel)
                            <a href="{{ route('users.index', array_merge(request()->except('page'), ['type' => $typeKey !== 'all' ? $typeKey : null])) }}"
                               class="{{ $tabClasses($userTypeTab === $typeKey) }}">
                                {{ $typeLabel }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Users</h3>
                    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Add User</a>
                </div>
                @if(session('success'))
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif
                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr>
                                <td class="px-4 py-2">{{ $user->id }}</td>
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{ ucfirst($user->user_type) }}</td>
                                <td class="px-4 py-2">{{ $user->department?->name }}</td>
                                <td class="px-4 py-2">{{ $user->vendor?->name }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-2 text-center text-gray-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>