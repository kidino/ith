<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tickets - Kanban View') }}
            </h2>
            @php
                $userType = Auth::user()->user_type ?? null;
            @endphp
            <div class="flex items-center space-x-3">
                <!-- View Toggle -->
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <a href="{{ route('tickets.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-200 rounded-l-lg {{ request()->routeIs('tickets.index') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        <!-- List Icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </a>
                    <a href="{{ route('tickets.kanban') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium border border-gray-200 rounded-r-lg {{ request()->routeIs('tickets.kanban') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        <!-- Kanban Icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                        </svg>
                    </a>
                </div>

                @if($userType !== 'vendor')
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow-sm hover:bg-blue-700 text-sm font-semibold">
                        + New Ticket
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="px-4 sm:px-6 lg:px-8 pt-4 pb-2">
        <div class="bg-white shadow-sm rounded-lg p-4">
            <form method="GET" action="{{ route('tickets.kanban') }}" id="kanbanFilters">
                <!-- Filters: Left side dropdowns, Right side column toggles -->
                <div class="flex items-start justify-between">
                    <!-- Left Side: Ownership and Category -->
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="ownership" class="block text-sm font-medium text-gray-700">Show</label>
                            <select name="ownership" id="ownership" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" onchange="this.form.submit()">
                                <option value="">All Tickets</option>
                                <option value="my_tickets" {{ request('ownership') == 'my_tickets' ? 'selected' : '' }}>My Tickets</option>
                                <option value="assigned_to_me" {{ request('ownership') == 'assigned_to_me' ? 'selected' : '' }}>Assigned To Me</option>
                            </select>
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($viewCategories as $id => $name)
                                    <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Side: Column Visibility -->
                    <div class="text-right">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Show Columns</label>
                        <div class="flex flex-wrap items-center gap-4 justify-end">
                            @php
                                $visibleColumns = request('columns', []);
                                if (empty($visibleColumns)) {
                                    // Default to all columns visible if none specified
                                    $visibleColumns = $statuses->pluck('name')->toArray();
                                }
                            @endphp
                            @foreach($statuses as $status)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                           name="columns[]" 
                                           value="{{ $status->name }}" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                           {{ in_array($status->name, $visibleColumns) ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <span class="ml-2 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $status->name)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kanban Board Container -->
    <div class="kanban-main px-4 sm:px-6 lg:px-8 pb-4">
            <div class="kanban-container overflow-x-auto overflow-y-hidden">
                <div class="kanban-board flex space-x-4 p-4 min-w-max">
                        @php
                            $visibleColumns = request('columns', []);
                            if (empty($visibleColumns)) {
                                // Default to all columns visible if none specified
                                $visibleColumns = $statuses->pluck('name')->toArray();
                            }
                        @endphp
                        @foreach($statuses as $status)
                            @if(in_array($status->name, $visibleColumns))
                            <div class="kanban-column flex-shrink-0 w-80 bg-gray-50 rounded-lg shadow-sm border border-gray-200 h-full flex flex-col">
                                <!-- Column Header -->
                                <div class="rounded-t-lg px-4 py-3 border-b border-gray-200 flex-shrink-0" style="background-color: {{ $status->color }}">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-white drop-shadow-sm">{{ ucwords(str_replace('_', ' ', $status->name)) }}</h3>
                                        <span class="bg-white bg-opacity-20 text-white text-xs px-2 py-1 rounded-full font-medium border border-white border-opacity-30">
                                            {{ count($ticketsByStatus[$status->id] ?? []) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Column Content -->
                                <div class="kanban-column-content flex-1 p-4 space-y-3 overflow-y-auto">
                                    @forelse($ticketsByStatus[$status->id] ?? [] as $ticket)
                                        <div class="kanban-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer" 
                                             onclick="window.location.href='{{ route('tickets.show', $ticket) }}'">
                                            <!-- Ticket ID and Priority -->
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-mono text-gray-500">#{{ $ticket->id }}</span>
                                                @if($ticket->priority)
                                                    <span class="text-xs px-2 py-1 rounded-full 
                                                        @if($ticket->priority === 'high') bg-red-100 text-red-800
                                                        @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ ucfirst($ticket->priority) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Ticket Title -->
                                            <h4 class="font-medium text-gray-900 mb-2 line-clamp-2">{{ $ticket->title }}</h4>

                                            <!-- Ticket Meta -->
                                            <div class="space-y-2">
                                                @if($ticket->category)
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                        </svg>
                                                        {{ $ticket->category->name }}
                                                    </div>
                                                @endif

                                                @if($ticket->user && $ticket->user->department)
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0h6"></path>
                                                        </svg>
                                                        {{ $ticket->user->department->name }}
                                                    </div>
                                                @endif

                                                <div class="flex items-center text-xs text-gray-600">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $ticket->user->name }}
                                                </div>

                                                <div class="flex items-center text-xs text-gray-600">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $ticket->created_at->diffForHumans() }}
                                                </div>
                                            </div>

                                            <!-- Assignees -->
                                            @if($ticket->assignees->count() > 0)
                                                <div class="mt-3 pt-3 border-t border-gray-100">
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                        <div class="flex -space-x-1">
                                                            @foreach($ticket->assignees->take(3) as $assignee)
                                                                <div class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs rounded-full border-2 border-white" 
                                                                     title="{{ $assignee->name }}">
                                                                    {{ substr($assignee->name, 0, 1) }}
                                                                </div>
                                                            @endforeach
                                                            @if($ticket->assignees->count() > 3)
                                                                <div class="inline-flex items-center justify-center w-6 h-6 bg-gray-500 text-white text-xs rounded-full border-2 border-white" 
                                                                     title="+{{ $ticket->assignees->count() - 3 }} more">
                                                                    +{{ $ticket->assignees->count() - 3 }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center text-gray-500 py-8">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-sm">No tickets</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .kanban-main {
            height: calc(100vh - 200px); /* Account for header + compact filters */
        }
        
        .kanban-container {
            height: 100%;
            -webkit-overflow-scrolling: touch;
        }
        
        .kanban-board {
            min-width: fit-content;
            height: 100%;
        }
        
        .kanban-column {
            height: 100%;
        }
        
        .kanban-card:hover {
            transform: translateY(-1px);
        }
    </style>
</x-app-layout>