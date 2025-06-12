<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket') }} : {{ $ticket->title }} (ID: {{ $ticket->id }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Ticket Details --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-2">{{ $ticket->title }}</h3>
                        <div class="mb-1"><span class="font-semibold">Status:</span> {{ $ticket->status?->name }}</div>
                        <div class="mb-1"><span class="font-semibold">Category:</span> {{ $ticket->category?->name }}</div>
                        <div class="mb-1"><span class="font-semibold">Department:</span> {{ $ticket->department?->name }}</div>
                        <div class="mb-1"><span class="font-semibold">Vendor:</span> {{ $ticket->vendor?->name }}</div>
                        <div class="mb-1"><span class="font-semibold">Assignees:</span>
                            @foreach($ticket->assignees as $assignee)
                                <span class="inline-block bg-gray-200 rounded px-2 py-1 text-xs text-gray-700 mr-1">
                                    {{ $assignee->name }}
                                    <form action="{{ route('tickets.removeAssignee', [$ticket, $assignee]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-1 text-red-500 hover:underline text-xs" onclick="return confirm('Remove assignee?')">&times;</button>
                                    </form>
                                </span>
                            @endforeach
                        </div>

                    {{-- Add Assignee Form with Autocomplete --}}
                    <form action="{{ route('tickets.addAssignee', $ticket) }}" method="POST" class="mt-2 flex items-center gap-2" id="add-assignee-form">
                        @csrf
                        <div class="relative w-64">
                            <input type="text" id="assignee-autocomplete" name="assignee_name" autocomplete="off"
                                class="rounded border-gray-300 w-full text-xs" placeholder="Type to search user..." required>
                            <input type="hidden" name="user_id" id="assignee-user-id">
                            <div id="autocomplete-results" class="absolute z-10 bg-white border border-gray-300 w-full mt-1 rounded shadow-lg hidden"></div>
                        </div>
                        <button type="submit" class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Add Assignee</button>
                    </form>

                        <div class="mb-1"><span class="font-semibold">Created by:</span> {{ $ticket->user?->name }}</div>
                        <div class="mb-1"><span class="font-semibold">Created at:</span> {{ $ticket->created_at->format('Y-m-d H:i') }}</div>
                        <div class="mt-4"><span class="font-semibold">Description:</span>
                            <div class="mt-1 text-gray-700">{{ $ticket->description }}</div>
                        </div>
                    </div>

                    {{-- Status Update Form --}}
                    <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}" class="mb-8">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-2">
                            <label for="ticket_status_id" class="font-semibold">Update Status:</label>
                            <select name="ticket_status_id" id="ticket_status_id" class="rounded border-gray-300">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" @if($ticket->ticket_status_id == $status->id) selected @endif>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Update</button>
                        </div>
                    </form>

                    {{-- Comments Area --}}
                    <div class="mb-8">
                        <h4 class="font-semibold mb-2">Comments</h4>
                        <div class="space-y-4">
                            @forelse($ticket->comments as $comment)
                                <div class="border rounded p-3 bg-gray-50">
                                    <div class="text-sm text-gray-600 mb-1">
                                        <span class="font-semibold">{{ $comment->user?->name ?? 'System' }}</span>
                                        <span class="ml-2 text-xs">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <div class="text-gray-800">{{ $comment->comment }}</div>
                                </div>
                            @empty
                                <div class="text-gray-400">No comments yet.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Add New Comment Form --}}
                    <form method="POST" action="{{ route('tickets.addComment', $ticket) }}">
                        @csrf
                        <div>
                            <label for="comment" class="block font-semibold mb-1">Add Comment</label>
                            <textarea name="comment" id="comment" rows="3" class="w-full rounded border-gray-300" required></textarea>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Submit</button>
                        </div>
                    </form>



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
                                    fetch("{{ route('users.autocomplete') }}?q=" + encodeURIComponent(query) + "&exclude=" + @json($ticket->assignees->pluck('id')))

                                        .then(response => response.json())
                                        .then(data => {
                                            results.innerHTML = '';
                                            if (data.length > 0) {
                                                data.forEach(user => {
                                                    const div = document.createElement('div');
                                                    div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-xs';
                                                    div.textContent = user.name + ' (' + user.email + ')';
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

                            // Hide results when clicking outside
                            document.addEventListener('click', function (e) {
                                if (!input.contains(e.target) && !results.contains(e.target)) {
                                    results.classList.add('hidden');
                                }
                            });

                            // Prevent form submit if no user selected
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
    </div>
</x-app-layout>
