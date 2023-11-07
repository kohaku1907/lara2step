<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-200">
    <header class="p-4 bg-blue-500 text-white">
        <!-- Your header here -->
    </header>
    <main class="p-4">
        @yield('content')
    </main>
    <footer class="p-4 bg-blue-500 text-white">
        <!-- Your footer here -->
    </footer>

    @stack('scripts')
</body>
</html>