{{-- filepath: c:\laragon\www\ith\resources\views\category\edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Category') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Name</label>
                        <input type="text" name="name" class="w-full rounded border-gray-300" required value="{{ old('name', $category->name) }}">
                        @error('name') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                        <a href="{{ route('categories.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>