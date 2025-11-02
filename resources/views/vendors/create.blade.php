<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Vendor') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('vendors.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Code</label>
                        <input type="text" name="code" class="w-full rounded border-gray-300" value="{{ old('code') }}">
                        @error('code') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Name</label>
                        <input type="text" name="name" class="w-full rounded border-gray-300" required value="{{ old('name') }}">
                        @error('name') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Phone Number</label>
                        <input type="text" name="phone_number" class="w-full rounded border-gray-300" value="{{ old('phone_number') }}">
                        @error('phone_number') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Address</label>
                        <input type="text" name="address" class="w-full rounded border-gray-300" value="{{ old('address') }}">
                        @error('address') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Person In Charge</label>
                        <input type="text" name="person_in_charge" class="w-full rounded border-gray-300" value="{{ old('person_in_charge') }}">
                        @error('person_in_charge') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded border-gray-300" value="{{ old('email') }}">
                        @error('email') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                        <a href="{{ route('vendors.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>