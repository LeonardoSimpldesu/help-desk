@props(['heading' => null])

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading ? $heading.' — HelpDesk' : 'HelpDesk' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2 text-slate-900">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500 text-sm font-bold text-white">HD</span>
            <span class="text-lg font-semibold">HelpDesk</span>
        </a>

        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5">
            @if ($heading)
                <h1 class="mb-6 text-xl font-semibold text-slate-900">{{ $heading }}</h1>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</html>
