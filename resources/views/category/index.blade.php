{{-- filepath: c:\laragon\www\ith\resources\views\category\index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

            
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Categories</h3>
                    <a href="{{ route('categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Add Category</a>
                </div>

                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr>
                                <td class="px-4 py-2">{{ $category->id }}</td>
                                <td class="px-4 py-2">{{ $category->name }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-400">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>