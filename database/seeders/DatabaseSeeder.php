<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Datos estructurales (roles, categorías, tasa, métodos de pago y usuario
     * administrador) siempre se siembran de forma idempotente. El catálogo de
     * demostración solo se siembra cuando SEED_DEMO_DATA=true (por defecto true
     * fuera de producción) para no borrar el inventario real al re-ejecutar.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            BulkTypeSeeder::class,
            CategorySeeder::class,
            ExchangeRateSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        $this->seedAdminUser();
        $this->seedSystemClient();

        $seedDemo = filter_var(
            env('SEED_DEMO_DATA', ! app()->isProduction()),
            FILTER_VALIDATE_BOOL
        );

        if ($seedDemo) {
            $this->call(ProductSeeder::class);
        } else {
            $this->command?->warn('SEED_DEMO_DATA=false → se omite el catálogo de demostración (ProductSeeder).');
        }
    }

    /**
     * Usuario administrador inicial. Las credenciales se toman del entorno; en
     * producción nunca se acepta la contraseña por defecto.
     */
    private function seedAdminUser(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@sexlandia.test');
        $password = (string) env('SEED_ADMIN_PASSWORD', 'password');

        if (app()->isProduction() && $password === 'password') {
            $password = Str::password(20);
            $this->command?->warn('SEED_ADMIN_PASSWORD no definido en producción. Se generó una contraseña temporal:');
            $this->command?->line("  Email:    {$email}");
            $this->command?->line("  Password: {$password}");
            $this->command?->warn('Cámbiala tras el primer inicio de sesión.');
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'dni' => env('SEED_ADMIN_DNI', '00000000'),
                'name' => env('SEED_ADMIN_NAME', 'Administrador'),
                'last_name' => env('SEED_ADMIN_LASTNAME', 'SEXLANDIA'),
                'phone_number' => env('SEED_ADMIN_PHONE', '00000000000'),
                'password' => Hash::make($password),
                'is_active' => true,
            ]
        );

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }

    /**
     * Usuario/cliente placeholder para ventas de mostrador sin cliente.
     * Nunca inicia sesión: se le asigna una contraseña aleatoria.
     */
    private function seedSystemClient(): void
    {
        $client = User::firstOrCreate(
            ['email' => 'ventas-sin-cliente@sexlandia.local'],
            [
                'dni' => '0',
                'name' => 'Venta sin cliente',
                'last_name' => 'sistema',
                'phone_number' => '0000000000',
                'password' => Hash::make(Str::password(32)),
                'is_active' => true,
            ]
        );

        if (! $client->hasRole('client')) {
            $client->assignRole('client');
        }
    }
}
