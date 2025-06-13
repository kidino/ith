<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tabs --}}
                    <div class="mb-6 border-b border-gray-200">
                        @php
                            $tab = $activeTab ?? 'all';
                            $tabClasses = function($active) {
                                return $active
                                    ? 'inline-block px-4 py-2 text-sm font-semibold text-blue-700 bg-blue-50 border-b-2 border-blue-600 rounded-t transition'
                                    : 'inline-block px-4 py-2 text-sm font-medium text-gray-500 hover:text-blue-700 hover:bg-gray-50 border-b-2 border-transparent rounded-t transition';
                            };
                            $userType = Auth::user()->user_type ?? null;
                        @endphp
                        <div class="flex items-center justify-between">
                            <nav class="flex space-x-2" aria-label="Tabs">
                                @if($userType === 'admin' || $userType === 'it')
                                    <a href="{{ route('tickets.index') }}"
                                       class="{{ $tabClasses($tab == 'all') }}">
                                        <svg class="inline w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path><path d="M16 3v4"></path><path d="M8 3v4"></path></svg>
                                        All Tickets
                                    </a>
                                @endif

                                @if($userType === 'admin' || $userType === 'it' || $userType === 'user')
                                    <a href="{{ route('tickets.mine') }}"
                                       class="{{ $tabClasses($tab == 'my') }}">
                                        <svg class="inline w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        My Tickets
                                    </a>
                                @endif

                                @if($userType === 'admin' || $userType === 'it' || $userType === 'vendor')
                                    <a href="{{ route('tickets.tasks') }}"
                                       class="{{ $tabClasses($tab == 'tasks') }}">
                                        <svg class="inline w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2"></path><path d="M12 12v.01"></path><path d="M17 21H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2-2 2 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"></path></svg>
                                        My Tasks
                                    </a>
                                @endif
                            </nav>
                            <a href="{{ route('tickets.create') }}" class="ml-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow-sm hover:bg-blue-700 text-sm font-semibold">
                                + New Ticket
                            </a>
                        </div>
                    </div>

                    {{-- Status & Category Filter Dropdowns --}}
                    @php
                        $statuses = \App\Models\TicketStatus::all();
                        $categories = \App\Models\Category::all();
                        $selectedStatus = request('status');
                        $selectedCategory = request('category');
                    @endphp
                    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-center">
                        {{-- Preserve other query params --}}
                        @foreach(request()->except(['status', 'category', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div>
                            <label for="status" class="mr-2 text-xs font-medium text-gray-700">Status:</label>
                            <select name="status" id="status" class="rounded border-gray-300 text-xs py-1 px-2" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" @if($selectedStatus == $status->id) selected @endif>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="category" class="mr-2 text-xs font-medium text-gray-700">Category:</label>
                            <select name="category" id="category" class="rounded border-gray-300 text-xs py-1 px-2" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if($selectedCategory == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="mb-4">
                    {{ $tickets->links() }}
                    </div>

                    @php
                        $sortable = [
                            'created_at' => 'Date',
                            'title' => 'Title',
                            'status' => 'Status',
                            'category' => 'Category',
                            'user' => 'By',
                            'department' => 'Department',
                        ];
                        $currentSort = request('sort', 'created_at');
                        $currentDirection = request('direction', 'desc');
                        function sortUrl($column) {
                            $direction = request('sort') === $column && request('direction') === 'asc' ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
                        }
                        function sortIcon($column) {
                            if (request('sort') === $column) {
                                return request('direction') === 'asc'
                                    ? '<span class="ml-1">&#9650;</span>'
                                    : '<span class="ml-1">&#9660;</span>';
                            }
                            return '';
                        }
                    @endphp

                    <table class="w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('created_at') }}" class="hover:underline flex items-center">
                                        Date {!! sortIcon('created_at') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('title') }}" class="hover:underline flex items-center">
                                        Title {!! sortIcon('title') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('status') }}" class="hover:underline flex items-center">
                                        Status {!! sortIcon('status') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('category') }}" class="hover:underline flex items-center">
                                        Category {!! sortIcon('category') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('department') }}" class="hover:underline flex items-center">
                                        Department {!! sortIcon('department') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ sortUrl('user') }}" class="hover:underline flex items-center">
                                        By {!! sortIcon('user') !!}
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Assignees
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($tickets as $ticket)
                                <tr class="@if($loop->even) bg-gray-50 @endif">
                                    <td class="px-4 py-2">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2">{{ $ticket->title }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColor = $ticket->status?->color;
                                            $statusTextColor = $statusColor ? 'text-white' : 'text-gray-800';
                                            $statusBgStyle = $statusColor ? "background: {$statusColor};" : '';
                                        @endphp
                                        @if($ticket->status)
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusTextColor }}" style="{{ $statusBgStyle }}">
                                                {{ $ticket->status->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $ticket->category?->name }}</td>
                                    <td class="px-4 py-2">{{ $ticket->user->department?->name }}</td>
                                    <td class="px-4 py-2">{{ $ticket->user?->name }}</td>
                                    <td class="px-4 py-2">
                                        @foreach($ticket->assignees as $assignee)
                                            <span class="inline-block bg-gray-200 rounded px-2 py-1 text-xs text-gray-700 mr-1 mb-1">{{ $assignee->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-2 text-center text-gray-400">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
