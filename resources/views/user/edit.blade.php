{{-- filepath: c:\laragon\www\ith\resources\views\user\edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" id="user-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Name</label>
                        <input type="text" name="name" class="w-full rounded border-gray-300" required value="{{ old('name', $user->name) }}">
                        @error('name') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded border-gray-300" required value="{{ old('email', $user->email) }}">
                        @error('email') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Password <span class="text-xs text-gray-400">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="w-full rounded border-gray-300">
                        @error('password') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded border-gray-300">
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold mb-1">User Type</label>
                        <select name="user_type" id="user_type" class="w-full rounded border-gray-300" required>
                            <option value="">-- Select Type --</option>
                            <option value="admin" @if(old('user_type', $user->user_type)=='admin') selected @endif>Admin</option>
                            <option value="it" @if(old('user_type', $user->user_type)=='it') selected @endif>IT</option>
                            <option value="user" @if(old('user_type', $user->user_type)=='user') selected @endif>User</option>
                            <option value="vendor" @if(old('user_type', $user->user_type)=='vendor') selected @endif>Vendor</option>
                        </select>
                        @error('user_type') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4" id="department-field" style="display: none;">
                        <label class="block font-semibold mb-1">Department</label>
                        <select name="department_id" class="w-full rounded border-gray-300">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @if(old('department_id', $user->department_id) == $department->id) selected @endif>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4" id="vendor-field" style="display: none;">
                        <label class="block font-semibold mb-1">Vendor</label>
                        <select name="vendor_id" class="w-full rounded border-gray-300">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @if(old('vendor_id', $user->vendor_id) == $vendor->id) selected @endif>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id') <div class="text-red-500 text-xs">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                        <a href="{{ route('users.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function toggleFields() {
            var type = document.getElementById('user_type').value;
            document.getElementById('department-field').style.display = (type === 'user') ? '' : 'none';
            document.getElementById('vendor-field').style.display = (type === 'vendor') ? '' : 'none';
        }
        document.getElementById('user_type').addEventListener('change', toggleFields);
        window.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-app-layout>