@props(['heading' => null, 'subheading' => null])

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading ? $heading.' — HelpDesk' : 'HelpDesk' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 antialiased">
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="grid w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-slate-900/10 sm:grid-cols-2">
            {{-- barra colorida no topo (mobile) --}}
            <div class="h-1.5 bg-gradient-to-r from-blue-500 via-blue-700 to-indigo-950 sm:hidden"></div>

            {{-- painel com ondas (desktop) --}}
            <div class="relative hidden overflow-hidden bg-gradient-to-br from-blue-600 via-blue-800 to-indigo-950 sm:block">
                <x-wave-pattern />
            </div>

            {{-- formulário --}}
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-16">
                <div class="mx-auto w-full max-w-sm">
                    <a href="{{ url('/') }}" class="mb-10 flex items-center justify-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-700">
                            <span class="h-3 w-3 rounded-full border-2 border-white"></span>
                        </span>
                        <span class="text-lg font-bold text-slate-900">HelpDesk</span>
                    </a>

                    @if ($heading)
                        <div class="mb-6">
                            <h1 class="text-lg font-semibold text-slate-900">{{ $heading }}</h1>
                            @if ($subheading)
                                <p class="mt-1 text-sm text-slate-500">{{ $subheading }}</p>
                            @endif
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
