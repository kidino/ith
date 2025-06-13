<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Ticket') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

            
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Title</label>
                        <input type="text" name="title" class="w-full rounded border-gray-300" required value="{{ old('title') }}">
                        @error('title') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>


                    <div class="mb-4">
                            <label class="block font-semibold mb-1">Category</label>
                            <select name="category_id" class="w-full rounded border-gray-300">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $id => $category)
                                    <option value="{{ $id }}" @if(old('category_id') == $id) selected @endif>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Description</label>
                        <textarea name="description" class="w-full rounded border-gray-300" rows="4">{{ old('description') }}</textarea>
                        @error('description') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>




                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create Ticket</button>
                        <a href="{{ route('tickets.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>