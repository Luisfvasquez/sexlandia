<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'cedula' => 'V-12345678',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('storefront', absolute: false));

    $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'dni' => 'V-12345678']);
    $this->assertTrue(auth()->user()->hasRole('client'));
    $this->assertDatabaseHas('clients', ['email' => 'test@example.com']);
});
