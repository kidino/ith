<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ITH Helpdesk System</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif; }
        .hero-bg {
            background: linear-gradient(90deg, #2563eb 0%, #10b981 100%);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 min-h-screen flex flex-col">
    <div class="hero-bg py-16">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">ITH Helpdesk System</h1>
            <p class="text-lg md:text-xl mb-8">A modern IT ticketing and support platform for your organization.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-white text-blue-700 font-semibold rounded shadow hover:bg-blue-50 transition">Go to Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-white text-blue-700 font-semibold rounded shadow hover:bg-blue-50 transition">Login</a>
            @endauth
        </div>
    </div>
    <div class="flex-1 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="mb-3">
                        <svg class="mx-auto h-10 w-10 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2"></path><path d="M12 12v.01"></path><path d="M17 21H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2-2 2 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Submit IT Requests</h3>
                    <p class="text-gray-500 text-sm">Easily create and track your IT support tickets for any issue or request.</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="mb-3">
                        <svg class="mx-auto h-10 w-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Collaborate & Resolve</h3>
                    <p class="text-gray-500 text-sm">Work with IT, vendors, and users to resolve issues quickly and efficiently.</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="mb-3">
                        <svg class="mx-auto h-10 w-10 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01"></path><circle cx="12" cy="12" r="10"></circle></svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Track & Analyze</h3>
                    <p class="text-gray-500 text-sm">Monitor ticket status, view analytics, and improve your IT support process.</p>
                </div>
            </div>
            <div class="mt-12 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} ITH Helpdesk System &mdash; Powered by Laravel & GitHub Copilot
                <br>
                <a href="https://github.com/kidino/ith" class="text-blue-500 hover:underline" target="_blank" rel="noopener">View on GitHub</a>
            </div>
        </div>
    </div>
</body>
</html>
