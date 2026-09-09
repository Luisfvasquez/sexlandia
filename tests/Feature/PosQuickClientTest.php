<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

it('quick-registers a client with the name typed in the modal', function () {
    $response = $this->actingAs($this->admin)->postJson(route('admin.pos.clients.store'), [
        'identification' => 'V-12345678',
        'name' => '  María Pérez  ',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonPath('client.name', 'María Pérez');

    $this->assertDatabaseHas('clients', [
        'identification' => 'V-12345678',
        'name' => 'María Pérez',
        'is_active' => true,
    ]);

    expect(Client::firstWhere('identification', 'V-12345678')->uuid)->not->toBeNull();
});

it('falls back to "Consumidor Final" when the name is empty or omitted', function (array $payload) {
    $this->actingAs($this->admin)
        ->postJson(route('admin.pos.clients.store'), ['identification' => 'V-777', ...$payload])
        ->assertOk()
        ->assertJsonPath('client.name', 'Consumidor Final');

    $this->assertDatabaseHas('clients', ['identification' => 'V-777', 'name' => 'Consumidor Final']);
})->with([
    'name omitted' => [[]],
    'name null' => [['name' => null]],
    'name blank' => [['name' => '   ']],
]);

it('rejects a duplicate identification', function () {
    Client::create([
        'uuid' => Str::uuid(),
        'identification' => 'V-999',
        'name' => 'Existente',
        'is_active' => true,
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.pos.clients.store'), ['identification' => 'V-999'])
        ->assertStatus(422);
});
