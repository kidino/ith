{{-- filepath: c:\laragon\www\ith\resources\views\ticket_status\create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Ticket Status') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                <form method="POST" action="{{ route('ticket-statuses.store') }}">
                    @csrf
                    <div class="mb-4 flex flex-col md:flex-row md:items-end md:gap-8">
                        <div class="w-full md:w-1/2 mb-4 md:mb-0">
                            <label class="block font-semibold mb-1">Name</label>
                            <input type="text" name="name" class="w-full rounded border-gray-300" required value="{{ old('name') }}">
                            @error('name') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                        </div>
                        <div class="w-full md:w-1/2">
                            <label class="block font-semibold mb-1">Color (CSS or HEX)</label>
                            <input type="color" name="color" class="w-full rounded border border-gray-300 h-10 p-1" value="{{ old('color') }}">
                            @error('color') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="default_status" value="1" class="rounded border-gray-300" {{ old('default_status') ? 'checked' : '' }}>
                            <span class="ml-2">Set as default status</span>
                        </label>
                        @error('default_status') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                        <a href="{{ route('ticket-statuses.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>