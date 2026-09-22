<x-layout.guest heading="Acesse o portal" subheading="Entre usando seu e-mail e senha cadastrados">
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-forms.input label="E-mail" name="email" type="email" placeholder="exemplo@mail.com" autofocus required />

        <x-forms.input label="Senha" name="password" type="password" placeholder="Digite sua senha" required />

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/40">
            Lembrar-me
        </label>

        <button
            type="submit"
            class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30 focus:ring-offset-2"
        >
            Entrar
        </button>
    </form>

    <div class="mt-8 border-t border-slate-100 pt-6">
        <h2 class="text-sm font-semibold text-slate-900">Ainda não tem uma conta?</h2>
        <p class="mt-1 text-xs text-slate-500">Cadastre agora mesmo</p>

        <a
            href="{{ route('register') }}"
            class="mt-3 block w-full rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
        >
            Criar conta
        </a>
    </div>
</x-layout.guest>
