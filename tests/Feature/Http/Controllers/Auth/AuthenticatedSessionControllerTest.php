<?php

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;

it('renders the login page for guests', function () {
    $response = $this->get(route('login'));

    $response->assertViewIs('auth.login');
});

it('redirects an authenticated user away from the login page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->get(route('login'));

    $response->assertRedirect('/');
});

it('logs a client in and redirects to the portal', function () {
    $client = Client::factory()->create();

    $response = $this->post(route('login'), [
        'email' => $client->email,
        'password' => 'password',
    ]);

    $response->assertRedirectToRoute('portal.index');
    $this->assertAuthenticatedAs($client);
});

it('logs staff in and redirects to the admin panel', function (string $role) {
    $staff = User::factory()->{$role}()->create();

    $response = $this->post(route('login'), [
        'email' => $staff->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/admin');
    $this->assertAuthenticatedAs($staff);
})->with([
    'admin' => 'admin',
    'technician' => 'technician',
]);

it('sets the remember cookie when remember me is checked', function () {
    $client = Client::factory()->create();

    $response = $this->post(route('login'), [
        'email' => $client->email,
        'password' => 'password',
        'remember' => 'on',
    ]);

    $response->assertCookie(auth()->guard()->getRecallerName());
});

it('rejects a wrong password with the invalid credentials message', function () {
    $client = Client::factory()->create();

    $response = $this->from(route('login'))->post(route('login'), [
        'email' => $client->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirectToRoute('login');
    $response->assertSessionHasErrors(['email' => 'Credenciais inválidas.']);
    $this->assertGuest();
});

it('signs out a user without a role right after logging in', function () {
    $user = User::factory()->create();

    $response = $this->followingRedirects()->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSeeText('Sua conta não possui acesso ao sistema.');
    $this->assertGuest();
});

it('locks the login after too many failed attempts', function () {
    Event::fake([Lockout::class]);
    $client = Client::factory()->create();
    $credentials = ['email' => $client->email, 'password' => 'wrong-password'];

    foreach (range(1, LoginRequest::MAX_ATTEMPTS) as $attempt) {
        $this->post(route('login'), $credentials);
    }

    $response = $this->post(route('login'), ['email' => $client->email, 'password' => 'password']);

    $response->assertSessionHasErrors('email');
    expect(session('errors')->first('email'))->toStartWith('O número limite de tentativas de login foi atingido.');
    $this->assertGuest();
    Event::assertDispatched(Lockout::class);
});

it('keeps other emails unlocked when one email is locked', function () {
    $locked = Client::factory()->create();
    $other = Client::factory()->create();

    foreach (range(1, LoginRequest::MAX_ATTEMPTS) as $attempt) {
        $this->post(route('login'), ['email' => $locked->email, 'password' => 'wrong-password']);
    }

    $response = $this->post(route('login'), ['email' => $other->email, 'password' => 'password']);

    $response->assertRedirectToRoute('portal.index');
    $this->assertAuthenticatedAs($other);
});

it('resets the failed attempts after a successful login', function () {
    $client = Client::factory()->create();

    foreach (range(1, LoginRequest::MAX_ATTEMPTS - 1) as $attempt) {
        $this->post(route('login'), ['email' => $client->email, 'password' => 'wrong-password']);
    }
    $this->post(route('login'), ['email' => $client->email, 'password' => 'password']);
    $this->post(route('logout'));

    $response = $this->post(route('login'), ['email' => $client->email, 'password' => 'wrong-password']);

    $response->assertSessionHasErrors(['email' => 'Credenciais inválidas.']);
});

it('rejects an empty payload', function () {
    $response = $this->post(route('login'), []);

    $response->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
});

it('rejects an email with an invalid format', function () {
    $response = $this->post(route('login'), [
        'email' => 'not-an-email',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('logs the user out and redirects to the login page', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($client)->post(route('logout'));

    $response->assertRedirectToRoute('login');
    $this->assertGuest();
});

it('redirects a guest trying to log out to the login page', function () {
    $response = $this->post(route('logout'));

    $response->assertRedirectToRoute('login');
});
