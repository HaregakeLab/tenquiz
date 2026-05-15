<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'MyApp'))</title>

    @if(config('app.debug'))
        {{-- 開発時: Tailwind CDN で全クラスが即座に使える --}}
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        {{-- 本番: ビルド済みの軽量CSS --}}
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    @yield('content')

    @stack('scripts')
</body>
</html>
