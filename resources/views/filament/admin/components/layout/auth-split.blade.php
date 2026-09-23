@props(['livewire' => null])

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="flex min-h-screen items-center justify-center bg-gray-100 p-4 dark:bg-gray-950 sm:p-6">
        <div class="grid w-full max-w-8xl min-h-[94svh] overflow-hidden rounded-3xl bg-white shadow-2xl shadow-gray-900/10 dark:bg-gray-900 sm:grid-cols-2">
            {{-- barra colorida no topo (mobile) --}}
            <div class="h-1.5 bg-gradient-to-r from-blue-500 via-blue-700 to-indigo-950 sm:hidden"></div>

            {{-- painel com ondas (desktop) --}}
            <div class="relative hidden overflow-hidden bg-gradient-to-br from-blue-600 via-blue-800 to-indigo-950 sm:block">
                <x-wave-pattern />
            </div>

            {{-- formulário --}}
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-16">
                <div class="mx-auto w-full max-w-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout.base>
