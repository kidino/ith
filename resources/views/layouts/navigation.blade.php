<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php
                        $userType = Auth::user()->user_type ?? null;
                    @endphp

                    @if($userType !== 'user' && $userType !== 'vendor')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets*')">
                        {{ __('Tickets') }}
                    </x-nav-link>

                    @if($userType === 'admin')
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <span>Settings</span>
                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('ticket-statuses.index')">
                                    {{ __('Ticket Statuses') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('categories.index')">
                                    {{ __('Categories') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('departments.index')">
                                    {{ __('Departments') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('users.index')">
                                    {{ __('Users') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('vendors.index')">
                                    {{ __('Vendors') }}
                                </x-dropdown-link>

                            </x-slot>
                        </x-dropdown>                    
                    </div>                        
                    @endif
                </div>


            </div>

            <!-- Settings Dropdown (user profile) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <!-- Notification -->

                @php
                    $unreadCount = Auth::user()->unreadNotifications->count();
                @endphp
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-400 border-b">
                            Notifications
                        </div>
                        @forelse(Auth::user()->notifications->take(5) as $notification)
                            <div class="px-4 py-3 text-xs {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 bg-blue-50' }} border-b hover:bg-gray-50">
                                <div class="font-semibold">{{ $notification->data['message'] ?? 'Notification' }}</div>
                                <div class="text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                @isset($notification->data['ticket_id'])
                                    @if(\App\Models\Ticket::find($notification->data['ticket_id']))
                                        <div class="mt-1">
                                            <a href="{{ route('tickets.show', $notification->data['ticket_id']) }}" 
                                               class="text-blue-600 hover:underline">View Ticket</a>
                                        </div>
                                    @endif
                                @endisset
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="inline mt-1">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:underline">Mark as read</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-3 text-xs text-gray-500">
                                No notifications
                            </div>
                        @endforelse
                        @if(Auth::user()->notifications->count() > 5)
                            <div class="px-4 py-2">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:underline">View all notifications</a>
                            </div>
                        @endif
                    </x-slot>
                </x-dropdown>

                <!-- /Notification -->


                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php
                $userType = Auth::user()->user_type ?? null;
            @endphp

            @if($userType !== 'user' && $userType !== 'vendor')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets*')">
                {{ __('Tickets') }}
            </x-responsive-nav-link>

            @if($userType === 'admin')
                <!-- Settings Section -->
                <div class="pt-2 border-t border-gray-200">
                    <div class="px-4 py-2">
                        <div class="font-medium text-sm text-gray-600 uppercase tracking-wide">Settings</div>
                    </div>
                    
                    <x-responsive-nav-link :href="route('ticket-statuses.index')" :active="request()->routeIs('ticket-statuses*')">
                        {{ __('Ticket Statuses') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories*')">
                        {{ __('Categories') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('departments.index')" :active="request()->routeIs('departments*')">
                        {{ __('Departments') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users*')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors*')">
                        {{ __('Vendors') }}
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
