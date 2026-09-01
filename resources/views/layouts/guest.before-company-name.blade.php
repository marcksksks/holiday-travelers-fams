<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Sign in') · Holiday Travelers Travel & Tours Inc.</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-body text-slate-700 antialiased">

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">

        {{-- Sky decoration --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-accent/20 to-transparent"></div>

        {{-- Sunset decoration --}}
        <div class="pointer-events-none absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-secondary/10 blur-3xl"></div>

        <div class="pointer-events-none absolute -left-32 top-1/3 h-80 w-80 rounded-full bg-accent/10 blur-3xl"></div>


        <div class="relative z-10 w-full max-w-md">

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-success/20 bg-success/10 px-4 py-3 text-sm text-green-700 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-error/20 bg-error/10 px-4 py-3 text-sm text-error shadow-sm">

                    <p class="mb-1 font-semibold">
                        Please check the following:
                    </p>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            @yield('content')

        </div>

    </main>

</body>
</html>
