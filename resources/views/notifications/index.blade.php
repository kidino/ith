<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded shadow-sm hover:bg-blue-700 text-sm font-semibold">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($notifications->hasPages())
                <div class="px-6 py-6 text-gray-900 pagination">
                    {{ $notifications->links() }}
                </div>
                @endif

                @forelse($notifications as $notification)
                    <div class="px-6 py-4 border-b {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-sm {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 font-semibold' }}">
                                    {{ $notification->data['message'] ?? 'Notification' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $notification->created_at->format('M j, Y g:i A') }} 
                                    ({{ $notification->created_at->diffForHumans() }})
                                </div>
                                @isset($notification->data['ticket_id'])
                                    <div class="mt-2">
                                        @php
                                            try {
                                                $ticketExists = \App\Models\Ticket::find($notification->data['ticket_id']);
                                            } catch (\Exception $e) {
                                                $ticketExists = false;
                                            }
                                        @endphp
                                        @if($ticketExists)
                                            <a href="{{ route('tickets.show', $notification->data['ticket_id']) }}" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs">
                                                View Ticket →
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">(Ticket no longer available)</span>
                                        @endif
                                    </div>
                                @endisset
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$notification->read_at)
                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                    <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:underline">
                                            Mark as read
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Read</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="mt-2 text-sm">No notifications yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>