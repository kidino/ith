<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Tickets by Status --}}
                <div class="bg-white rounded-xl shadow p-6 flex flex-col col-span-1">
                    <h3 class="font-semibold mb-4 text-gray-700">Tickets by Status</h3>
                    <ul class="divide-y divide-gray-100">
                        @foreach($statusCounts as $statusName => $count)
                            @php
                                $statusColor = $statusColors[$statusName] ?? '#6b7280';
                            @endphp
                            <li class="flex items-center justify-between py-2">
                                <span class="flex items-center">
                                    <span class="inline-block w-3 h-3 rounded mr-2" style="background: {{ $statusColor }}"></span>
                                    <span class="text-gray-700">{{ $statusName }}</span>
                                </span>
                                <span class="font-bold text-gray-900">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                {{-- Pie Chart: Tickets by Category --}}
                <div class="bg-white rounded-xl shadow p-6 flex flex-col col-span-1">
                    <h3 class="font-semibold mb-4 text-gray-700">Tickets by Category (%)</h3>
                    <div class="flex-1 flex items-center justify-center">
                        <canvas id="ticketsByCategoryPie"></canvas>
                    </div>
                </div>
                {{-- My Tasks/My Tickets widgets stacked vertically, 1/3 width --}}
                <div class="flex flex-col gap-6 col-span-1">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl shadow flex items-center p-6">
                        <div class="flex-1">
                            <div class="text-sm text-blue-100 font-semibold">My Tasks</div>
                            <div class="text-4xl font-bold text-white">{{ $assignedToMeCount }}</div>
                        </div>
                        <div class="ml-4 flex items-center justify-center w-14 h-14 rounded-full bg-blue-600/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2"></path><path d="M12 12v.01"></path><path d="M17 21H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2-2 2 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow flex items-center p-6">
                        <div class="flex-1">
                            <div class="text-sm text-green-100 font-semibold">My Tickets</div>
                            <div class="text-4xl font-bold text-white">{{ $createdByMeCount }}</div>
                        </div>
                        <div class="ml-4 flex items-center justify-center w-14 h-14 rounded-full bg-green-600/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Tickets by Department --}}
                <div class="bg-white rounded-xl shadow p-6 flex flex-col">
                    <h3 class="font-semibold mb-4 text-gray-700">Tickets by Department</h3>
                    <div class="flex-1 flex items-center justify-center">
                        <canvas id="ticketsByDepartmentBar"></canvas>
                    </div>
                </div>
                {{-- Bar Chart: Tickets by Category --}}
                <div class="bg-white rounded-xl shadow p-6 flex flex-col">
                    <h3 class="font-semibold mb-4 text-gray-700">Tickets by Category (Count)</h3>
                    <div class="flex-1 flex items-center justify-center">
                        <canvas id="ticketsByCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        // Prepare data for charts
        $categoryLabels = array_values($categoryCounts->pluck('name')->toArray());
        $categoryCountsArr = array_values($categoryCounts->pluck('count')->toArray());
        $categoryColors = ['#2563eb', '#f59e42', '#fbbf24', '#10b981', '#6b7280', '#ef4444', '#6366f1', '#14b8a6', '#f472b6', '#facc15'];
        $pieColors = array_slice($categoryColors, 0, count($categoryLabels));

        $departmentLabels = array_values($departmentCounts->pluck('name')->toArray());
        $departmentCountsArr = array_values($departmentCounts->pluck('count')->toArray());
        $departmentColors = array_slice($categoryColors, 0, count($departmentLabels));
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pie Chart
        new Chart(document.getElementById('ticketsByCategoryPie'), {
            type: 'pie',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    data: @json($categoryCountsArr),
                    backgroundColor: @json($pieColors),
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Bar Chart
        new Chart(document.getElementById('ticketsByCategoryBar'), {
            type: 'bar',
            data: {
                labels: @json($categoryLabels),
                datasets: [{
                    label: 'Tickets',
                    data: @json($categoryCountsArr),
                    backgroundColor: @json($pieColors),
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Bar Chart: Tickets by Department
        new Chart(document.getElementById('ticketsByDepartmentBar'), {
            type: 'bar',
            data: {
                labels: @json($departmentLabels),
                datasets: [{
                    label: 'Tickets',
                    data: @json($departmentCountsArr),
                    backgroundColor: @json($departmentColors),
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
