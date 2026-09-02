<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPanelController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClientPanelController::class, 'storefront'])->name('storefront');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/index', 'index')->name('admin.index');
        });
        Route::resource('admins', AdminController::class)->names('admin.admins');

        Route::controller(ProductController::class)->group(function () {
            Route::delete('products/{image}/image', 'destroyImage')->name('admin.products.destroyImage');
            Route::get('forzar-actualizacion-dolar', 'forzarActualizacionDolar')->name('admin.products.forzarActualizacionDolar');
        });
        Route::resource('products', ProductController::class)->names('admin.products');

        Route::controller(ClientController::class)->group(function () {
            Route::post('clients/{client}/abonos', 'registerAbono')->name('admin.clients.registerAbono');
            Route::patch('orders/{order}/verification', 'updateOrderVerification')->name('admin.orders.updateVerification');
        });
        Route::resource('clients', ClientController::class)->names('admin.clients');

        Route::controller(CategoryController::class)->group(function () {
            Route::post('categories/quick-store', 'quickStore')->name('admin.categories.quickStore');
        });
        Route::resource('categories', CategoryController::class)->names('admin.categories');

        Route::resource('suppliers', SupplierController::class)->names('admin.suppliers');
        Route::resource('inventories', InventoryController::class)->names('admin.inventories');

        Route::controller(PurchaseController::class)->group(function () {
            Route::get('purchases/index', 'index')->name('admin.purchases.index');
            Route::get('purchases/create', 'create')->name('admin.purchases.create');
            Route::post('purchases', 'store')->name('admin.purchases.store');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::post('orders/{order}/approve', 'approve')->name('admin.orders.approve');
            Route::post('orders/{order}/reject', 'reject')->name('admin.orders.reject');
            Route::post('orders/{order}/proof', 'uploadProof')->name('admin.orders.proof');
            Route::post('orders/{order}/deliver', 'deliver')->name('admin.orders.deliver');

            // Rutas API para el Punto de Venta (POS)
            Route::get('pos/products/search', 'searchProduct')->name('admin.pos.products.search');
            Route::get('pos/clients/search', 'searchClient')->name('admin.pos.clients.search');
            Route::post('pos/clients/quick-store', 'storeClient')->name('admin.pos.clients.store');
        });
        Route::resource('orders', OrderController::class)->names('admin.orders');

        Route::resource('payment_methods', PaymentMethodController::class)->only(['store', 'update', 'destroy'])->names('admin.payment_methods');

        // Configuración de Roles y Asignaciones
        Route::prefix('config')->group(function () {
            Route::resource('roles', RoleController::class)->names('admin.roles');
            Route::get('users-roles', [UserRoleController::class, 'index'])->name('admin.users-roles.index');
            Route::post('users-roles/{user}', [UserRoleController::class, 'update'])->name('admin.users-roles.update');
        });
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas exclusivas del panel de cliente
Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->group(function () {
        Route::get('/dashboard', [ClientPanelController::class, 'dashboard'])->name('client.dashboard');
        Route::get('/productos', [ClientPanelController::class, 'products'])->name('client.products');
        Route::post('/checkout', [ClientPanelController::class, 'checkout'])->name('client.checkout');
        Route::get('/checkout', [ClientPanelController::class, 'checkoutView'])->name('client.checkout.view');
        Route::get('/compras', [ClientPanelController::class, 'purchases'])->name('client.purchases');
        Route::get('/facturas', [ClientPanelController::class, 'invoices'])->name('client.invoices');
        Route::post('/facturas/reportar-pago', [ClientPanelController::class, 'reportPayment'])->name('client.invoices.report');
        Route::get('/perfil', [ClientPanelController::class, 'profile'])->name('client.profile');
        Route::patch('/perfil', [ClientPanelController::class, 'profileUpdate'])->name('client.profile.update');
    });

// Rutas del módulo de repartidores / delivery
Route::middleware(['auth', 'role:delivery|admin'])
    ->prefix('delivery')
    ->group(function () {
        Route::get('/dashboard', [DeliveryController::class, 'dashboard'])->name('delivery.dashboard');
        Route::get('/disponibles', [DeliveryController::class, 'availableOrders'])->name('delivery.available');
        Route::post('/claim/{id}', [DeliveryController::class, 'claimOrder'])->name('delivery.claim');
        Route::get('/activas', [DeliveryController::class, 'activeDeliveries'])->name('delivery.active');
        Route::get('/orden/{id}', [DeliveryController::class, 'show'])->name('delivery.show');
        Route::post('/complete/{id}', [DeliveryController::class, 'completeDelivery'])->name('delivery.complete');
        Route::get('/historial', [DeliveryController::class, 'history'])->name('delivery.history');
    });

require __DIR__.'/auth.php';
