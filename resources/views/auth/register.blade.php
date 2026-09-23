<x-layout.guest heading="Crie sua conta" subheading="Preencha os dados abaixo para criar sua conta">
    <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
        @csrf

        <x-forms.input label="Nome" name="name" type="text" placeholder="Digite o nome completo" autofocus required />

        <x-forms.input label="E-mail" name="email" type="email" placeholder="exemplo@mail.com" required />

        <x-forms.input label="Telefone" name="phone" type="tel" placeholder="(00) 00000-0000" inputmode="numeric" x-data
            x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999'" />

        <x-forms.input label="Senha" name="password" type="password" placeholder="Digite sua senha" hint="Mínimo de 8 caracteres" required />

        <x-forms.input label="Confirmar senha" name="password_confirmation" type="password" placeholder="Digite sua senha novamente" required />

        <button
            type="submit"
            class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30 focus:ring-offset-2"
        >
            Cadastrar
        </button>
    </form>

    <div class="mt-8 border-t border-slate-100 pt-6">
        <h2 class="text-sm font-semibold text-slate-900">Já tem uma conta?</h2>
        <p class="mt-1 text-xs text-slate-500">Entre agora mesmo</p>

        <a
            href="{{ route('login') }}"
            class="mt-3 block w-full rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
        >
            Acessar conta
        </a>
    </div>
</x-layout.guest>
