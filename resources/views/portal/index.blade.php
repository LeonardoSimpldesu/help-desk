<x-layout.guest :heading="'Bem-vindo, '.$user->name">
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <p class="text-sm text-slate-600">Meus chamados chegam na Fase 4.</p>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button
            type="submit"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
        >
            Sair
        </button>
    </form>
</x-layout.guest>
