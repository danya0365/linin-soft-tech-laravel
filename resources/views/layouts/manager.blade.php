<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Supervisor - LinenSoftTech') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
    <div id="app">
        <x-header />
        
        <main class="py-4">
            @yield('content')
        </main>
        <footer>
            <div class="container d-flex justify-content-between py-4 my-4 border-top">
                <p>&copy; 2021 {{ config('app.name') }}, Inc. All rights reserved.</p>
                <div class="d-flex">
                    v{{ config('app.version') }} ({{ config('app.build') }})
                    &nbsp;
                    <a href="https://www.facebook.com/marosdee7" target="_blank">
                    พัฒนาโดย: Marosdee7
                    </a>
                </div>
            </div>
        </footer>
    </div>
        
    {{-- Mini Chat Popover --}}
    <x-mini-chat />

    {{-- Environment Debug Component --}}
    <x-env-debug />
</body>
</html>
