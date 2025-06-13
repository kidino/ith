<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket') }} : {{ $ticket->title }} (ID: {{ $ticket->id }})
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $ticket->title }}</h3>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-semibold">Status:</span>
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
                            </div>
                            <div>
                                <span class="font-semibold">Category:</span> {{ $ticket->category?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 text-sm text-gray-500">
                        <div><span class="font-semibold">Created by:</span> {{ $ticket->user?->name ?? '-' }}</div>
                        <div><span class="font-semibold">Department:</span> {{ $ticket->user?->department->name ?? '-' }}</div>
                        <div><span class="font-semibold">Created at:</span> {{ $ticket->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <div class="mb-6">
                            <div class="font-semibold text-gray-700 mb-2">Description</div>
                            <div class="bg-gray-50 rounded p-4 text-gray-800 min-h-[60px]">{{ $ticket->description }}</div>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-700">Comments</h4>
                            </div>
                            <div class="space-y-4">
                                @forelse($ticket->comments as $comment)
                                    <div class="border rounded p-3 bg-gray-50">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold text-gray-600">{{ $comment->user?->name ?? 'System' }}</span>
                                            <span class="text-xs text-gray-400">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div class="text-gray-800">{{ $comment->comment }}</div>
                                    </div>
                                @empty
                                    <div class="text-gray-400">No comments yet.</div>
                                @endforelse
                            </div>
                        </div>

                        <form method="POST" action="{{ route('tickets.addComment', $ticket) }}" class="bg-gray-50 rounded p-4">
                            @csrf
                            <label for="comment" class="block font-semibold mb-1">Add Comment</label>
                            <textarea name="comment" id="comment" rows="3" class="w-full rounded border-gray-300 mb-2" required></textarea>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Submit</button>
                        </form>
                    </div>

                    <div>
                        {{-- Category Update Form --}}
                        <div class="mb-6">
                            @php
                                $userType = Auth::user()->user_type ?? null;
                            @endphp
                            @if($userType === 'admin' || $userType === 'it')
                                <form method="POST" action="{{ route('tickets.updateCategory', $ticket) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label for="category_id" class="font-semibold text-gray-700">Category:</label>
                                    <select name="category_id" id="category_id" class="rounded border-gray-300 text-xs" onchange="this.form.submit()">
                                        @foreach($viewCategories as $id => $name)
                                            <option value="{{ $id }}" @if($ticket->category_id == $id) selected @endif>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <noscript>
                                        <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Update</button>
                                    </noscript>
                                </form>
                            @else
                                <div>
                                    <span class="font-semibold text-gray-700">Category:</span>
                                    <span class="ml-2 text-xs">{{ $ticket->category?->name ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                        {{-- Assignees Interface --}}
                        <div class="mb-6">
                            <div class="font-semibold text-gray-700 mb-2">Assignees</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($ticket->assignees as $assignee)
                                    @php
                                        $badgeClass = $assignee->user_type === 'vendor'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-gray-200 text-gray-700';
                                    @endphp
                                    <span class="inline-flex {{ $badgeClass }} rounded px-2 py-1 text-xs">
                                        <div class="inline-flex flex-col">
                                            <strong>{{ $assignee->name }}</strong>
                                            @if($assignee->user_type === 'vendor')
                                                <span class="w-full">{{ $assignee->vendor->name }}</span>
                                            @endif
                                        </div>
                                        @if($userType === 'admin' || $userType === 'it')
                                            <form action="{{ route('tickets.removeAssignee', [$ticket, $assignee]) }}" method="POST" class="inline ml-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline text-xs" title="Remove" onclick="return confirm('Remove assignee?')">&times;</button>
                                            </form>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                            @if($userType === 'admin' || $userType === 'it')
                                <form action="{{ route('tickets.addAssignee', $ticket) }}" method="POST" class="mt-3 flex items-center gap-2" id="add-assignee-form">
                                    @csrf
                                    <div class="relative w-full">
                                        <input type="text" id="assignee-autocomplete" name="assignee_name" autocomplete="off"
                                            class="rounded border-gray-300 w-full text-xs" placeholder="Type to search user..." required>
                                        <input type="hidden" name="user_id" id="assignee-user-id">
                                        <div id="autocomplete-results" class="absolute z-10 bg-white border border-gray-300 w-full mt-1 rounded shadow-lg hidden"></div>
                                    </div>
                                    <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Add</button>
                                </form>
                            @endif
                        </div>

                        <div class="mb-6">
                            @if($userType === 'admin' || $userType === 'it')
                                <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}" class="">
                                    @csrf
                                    @method('PATCH')
                                    <div class="font-semibold text-gray-700 mb-2">Update Status</div>
                                    <div class="relative w-full">
                                        <select name="ticket_status_id" id="ticket_status_id" class="text-xs rounded border-gray-300">
                                            @foreach($statuses as $id => $status)
                                                <option value="{{ $id }}" @if($ticket->ticket_status_id == $id) selected @endif>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Update</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const input = document.getElementById('assignee-autocomplete');
                        const results = document.getElementById('autocomplete-results');
                        const userIdInput = document.getElementById('assignee-user-id');
                        let timeout = null;

                        input.addEventListener('input', function () {
                            clearTimeout(timeout);
                            const query = this.value;
                            userIdInput.value = '';
                            if (query.length < 2) {
                                results.innerHTML = '';
                                results.classList.add('hidden');
                                return;
                            }
                            timeout = setTimeout(() => {
                                fetch("{{ route('users.autocomplete') }}?q=" + encodeURIComponent(query) + "&exclude=" + @json($ticket->assignees->pluck('id')) + "&user_types[]=it&user_types[]=vendor")
                                    .then(response => response.json())
                                    .then(data => {
                                        results.innerHTML = '';
                                        if (data.length > 0) {
                                            data.forEach(user => {
                                                const div = document.createElement('div');
                                                div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-xs';
                                                if(user.user_type === 'it') {
                                                    div.innerHTML = '[IT] <strong>' + user.name +'</strong>';
                                                } else if(user.user_type === 'vendor') {
                                                    div.innerHTML = '[VN] <strong>'+ user.name + '</strong><br>' + user.vendor.name;
                                                }

                                                div.dataset.userId = user.id;
                                                div.addEventListener('click', function () {
                                                    input.value = user.name;
                                                    userIdInput.value = user.id;
                                                    results.innerHTML = '';
                                                    results.classList.add('hidden');
                                                });
                                                results.appendChild(div);
                                            });
                                            results.classList.remove('hidden');
                                        } else {
                                            results.classList.add('hidden');
                                        }
                                    });
                            }, 200);
                        });

                        document.addEventListener('click', function (e) {
                            if (!input.contains(e.target) && !results.contains(e.target)) {
                                results.classList.add('hidden');
                            }
                        });

                        document.getElementById('add-assignee-form').addEventListener('submit', function(e) {
                            if (!userIdInput.value) {
                                e.preventDefault();
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
