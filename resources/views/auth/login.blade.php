<x-layout.guest heading="Entrar na sua conta">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-forms.input label="E-mail" name="email" type="email" autofocus required />

        <x-forms.input label="Senha" name="password" type="password" required />

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-500 focus:ring-blue-500/40">
            Lembrar-me
        </label>

        <button
            type="submit"
            class="w-full rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-2"
        >
            Entrar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Não tem uma conta?
        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700">Criar conta</a>
    </p>
</x-layout.guest>
