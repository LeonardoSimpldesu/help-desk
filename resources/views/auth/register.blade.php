<x-layout.guest heading="Criar sua conta">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-forms.input label="Nome" name="name" type="text" autofocus required />

        <x-forms.input label="E-mail" name="email" type="email" required />

        <x-forms.input label="Telefone" name="phone" type="tel" />

        <x-forms.input label="Senha" name="password" type="password" required />

        <x-forms.input label="Confirmar senha" name="password_confirmation" type="password" required />

        <button
            type="submit"
            class="w-full rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-2"
        >
            Cadastrar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        Já tem uma conta?
        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700">Já tenho conta</a>
    </p>
</x-layout.guest>
