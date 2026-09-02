<?php

namespace App\Actions\Client;

use App\Events\ClientCreated;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterClientAction
{
    public function handle(array $validated, bool $createAccount = false): Client
    {
        return DB::transaction(function () use ($validated, $createAccount) {
            $userId = null;

            if ($createAccount) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make(trim($validated['identification'])),
                ]);

                $userId = $user->id;
            }

            $client = Client::create([
                'uuid' => Str::uuid(),
                'user_id' => $userId,
                'name' => $validated['name'],
                'identification' => trim($validated['identification']),
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'],
                'is_active' => true,
            ]);

            event(new ClientCreated($client));

            return $client;
        });
    }
}
