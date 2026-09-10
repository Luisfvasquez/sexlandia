<?php

namespace Database\Seeders;

use App\Models\Bulk;
use App\Models\BulkType;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Catálogo demo de SEXLANDIA (sex shop) con imágenes optimizadas.
     */
    public function run(): void
    {
        // Salvaguarda: este seeder BORRA todo el catálogo. En producción solo se
        // ejecuta si SEED_DEMO_DATA=true o si aún no hay productos cargados.
        $allowDemo = filter_var(env('SEED_DEMO_DATA', ! app()->isProduction()), FILTER_VALIDATE_BOOL);

        if (! $allowDemo && Product::query()->exists()) {
            $this->command?->warn('ProductSeeder omitido: ya hay productos y SEED_DEMO_DATA no está activo.');

            return;
        }

        Schema::disableForeignKeyConstraints();
        Product::truncate();
        Inventory::truncate();
        Bulk::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $bulkTypes = BulkType::all();
        if ($bulkTypes->isEmpty()) {
            $this->call(BulkTypeSeeder::class);
            $bulkTypes = BulkType::all();
        }
        $bulkTypesMap = $bulkTypes->pluck('id', 'slug')->toArray();

        $creator = User::first() ?? User::create([
            'dni' => '29873955',
            'name' => 'Luis',
            'last_name' => 'Vasquez',
            'phone_number' => '04145018145',
            'email' => 'wueyluis@gmail.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Bank de productos por slug de categoría (precios en USD; se almacenan tal cual)
        $productBank = [
            'succionadores' => [
                ['name' => 'Satisfyer Pro 2 Generación 3', 'brand' => 'Satisfyer', 'cost' => 28, 'price' => 45, 'desc' => 'Succionador de clítoris con tecnología Liquid Air, 11 intensidades y cabezal de silicona médica. Sumergible IPX7 y recargable por imán.'],
                ['name' => 'Satisfyer Penguin Next Generation', 'brand' => 'Satisfyer', 'cost' => 26, 'price' => 42, 'desc' => 'Diseño ergonómico tipo pingüino con ondas de presión sin contacto. 11 programas, silencioso y a prueba de agua.'],
                ['name' => 'Satisfyer Orca Succionador', 'brand' => 'Satisfyer', 'cost' => 30, 'price' => 49, 'desc' => 'Succionador premium con silueta envolvente, boquilla suave y control intuitivo de intensidad.'],
                ['name' => 'Womanizer Starlet 3', 'brand' => 'Womanizer', 'cost' => 40, 'price' => 65, 'desc' => 'Tecnología Pleasure Air con 4 niveles de intensidad. Compacto, discreto y perfecto para iniciarse.'],
                ['name' => 'Satisfyer Curvy 1+ con App', 'brand' => 'Satisfyer', 'cost' => 34, 'price' => 55, 'desc' => 'Succionador con conexión Bluetooth y control desde la app Satisfyer Connect. Cabezal flexible ajustable.'],
                ['name' => 'Succionador Rosa Recargable', 'brand' => 'SEXLANDIA', 'cost' => 14, 'price' => 25, 'desc' => 'Modelo compacto de silicona suave con 10 modos de succión. Carga USB y bolsa de viaje incluida.'],
            ],
            'vibradores' => [
                ['name' => 'Satisfyer Heat Flex 4 Rabbit', 'brand' => 'Satisfyer', 'cost' => 33, 'price' => 52, 'desc' => 'Vibrador conejo flexible con función de calor a 40°C y doble motor para punto G y clítoris. App Connect.'],
                ['name' => 'Xmoment X-Puff Dual', 'brand' => 'Xmoment', 'cost' => 22, 'price' => 36, 'desc' => 'Estimulador dual: succión clitorial + vibración interna para punto G. Silicona líquida y 7 patrones.'],
                ['name' => 'Satisfyer Shiny Petal Wearable', 'brand' => 'Satisfyer', 'cost' => 28, 'price' => 46, 'desc' => 'Vibrador ergonómico de uso ponible con control por app a distancia. Discreto y silencioso.'],
                ['name' => 'Vibrador Bala Recargable', 'brand' => 'SEXLANDIA', 'cost' => 8, 'price' => 16, 'desc' => 'Bala vibradora potente y silenciosa con 10 funciones. Ideal para estimulación puntual y juego en pareja.'],
                ['name' => 'Conejo Clásico Multivelocidad', 'brand' => 'CalExotics', 'cost' => 19, 'price' => 32, 'desc' => 'Vibrador conejo con rotación de perlas, orejas estimuladoras y varios niveles de vibración.'],
                ['name' => 'Vibrador Varita Mágica Recargable', 'brand' => 'SEXLANDIA', 'cost' => 24, 'price' => 39, 'desc' => 'Cabezal grande y flexible, vibración profunda de grado wand. 20 modos y carga rápida USB.'],
            ],
            'lubricantes' => [
                ['name' => 'ID Glide Lubricante Base Agua 130ml', 'brand' => 'ID Lubricants', 'cost' => 7, 'price' => 13, 'desc' => 'Lubricante premium base agua, textura sedosa de larga duración. Compatible con juguetes y preservativos.'],
                ['name' => 'ID Millennium Base Silicona 65ml', 'brand' => 'ID Lubricants', 'cost' => 9, 'price' => 17, 'desc' => 'Lubricante base silicona ultra resbaladizo y a prueba de agua. Una gota rinde muchísimo.'],
                ['name' => 'Trío ID Lubricantes Travel Pack', 'brand' => 'ID Lubricants', 'cost' => 12, 'price' => 22, 'desc' => 'Set de 3 lubricantes de viaje: sensación, calor y clásico base agua. Perfecto para probar.'],
                ['name' => 'Pjur Original Base Silicona 100ml', 'brand' => 'Pjur', 'cost' => 15, 'price' => 26, 'desc' => 'Lubricante alemán de silicona con dimeticona, sin sabor ni olor. Muy duradero.'],
                ['name' => 'Lubricante Efecto Calor 100ml', 'brand' => 'SEXLANDIA', 'cost' => 6, 'price' => 12, 'desc' => 'Base agua con efecto calor suave que se intensifica con el movimiento y el aliento.'],
                ['name' => 'Gel Potenciador Sensación 30ml', 'brand' => 'SEXLANDIA', 'cost' => 8, 'price' => 15, 'desc' => 'Gel estimulante de aplicación tópica para aumentar la sensibilidad y el flujo sanguíneo.'],
            ],
            'juegos-y-parejas' => [
                ['name' => 'CalExotics Furry Cuffs Esposas de Peluche', 'brand' => 'CalExotics', 'cost' => 10, 'price' => 18, 'desc' => 'Esposas ultrasuaves forradas en peluche con cierre metálico y llave. Cómodas para juego de rol.'],
                ['name' => 'Anillo Vibrador para Pareja', 'brand' => 'SEXLANDIA', 'cost' => 7, 'price' => 14, 'desc' => 'Anillo de silicona elástica con bala vibradora recargable. Prolonga la erección y estimula a ambos.'],
                ['name' => 'Satisfyer Double Joy con App', 'brand' => 'Satisfyer', 'cost' => 32, 'price' => 52, 'desc' => 'Vibrador para parejas de uso simultáneo durante la penetración. Doble motor y control por app.'],
                ['name' => 'Kit BDSM Iniciación 7 Piezas', 'brand' => 'SEXLANDIA', 'cost' => 18, 'price' => 32, 'desc' => 'Antifaz, esposas, cuerda, látigo, pinzas y plumas en un estuche. Para explorar en confianza.'],
                ['name' => 'Dado del Amor y Cartas Picantes', 'brand' => 'SEXLANDIA', 'cost' => 4, 'price' => 9, 'desc' => 'Juego de mesa para parejas con retos y posiciones. Rompe la rutina con humor.'],
                ['name' => 'Huevo Vibrador con Control Remoto', 'brand' => 'CalExotics', 'cost' => 16, 'price' => 28, 'desc' => 'Huevo de silicona con mando inalámbrico de largo alcance. Perfecto para juego discreto fuera de casa.'],
            ],
            'lenceria' => [
                ['name' => 'Body de Encaje Transparente', 'brand' => 'SEXLANDIA', 'cost' => 9, 'price' => 19, 'desc' => 'Body de encaje floral con espalda descubierta y broches en la entrepierna. Tallas S a XL.'],
                ['name' => 'Conjunto Bralette y Liguero', 'brand' => 'SEXLANDIA', 'cost' => 12, 'price' => 24, 'desc' => 'Set de tres piezas: bralette, tanga y liguero ajustable en satén y encaje.'],
                ['name' => 'Baby Doll Satén con Tanga', 'brand' => 'SEXLANDIA', 'cost' => 10, 'price' => 21, 'desc' => 'Baby doll de satén suave con detalles de encaje y tanga a juego. Corte favorecedor.'],
                ['name' => 'Disfraz Enfermera Sexy', 'brand' => 'SEXLANDIA', 'cost' => 13, 'price' => 26, 'desc' => 'Disfraz de rol de 3 piezas con vestido ajustado, gorro y stockings. Juego y fantasía.'],
                ['name' => 'Medias de Rejilla con Liga', 'brand' => 'SEXLANDIA', 'cost' => 3, 'price' => 8, 'desc' => 'Medias altas de rejilla con banda de silicona autoadherente. Talla única elástica.'],
                ['name' => 'Kimono Corto de Malla', 'brand' => 'SEXLANDIA', 'cost' => 8, 'price' => 17, 'desc' => 'Bata corta translúcida con mangas amplias y cinturón. Ligera y sensual.'],
            ],
            'bienestar-intimo' => [
                ['name' => 'Pure Instinct Hair & Body Mist', 'brand' => 'Pure Instinct', 'cost' => 16, 'price' => 28, 'desc' => 'Bruma con feromonas para cabello y cuerpo. Aroma envolvente unisex de larga fijación.'],
                ['name' => 'Perfume con Feromonas Roll-On 10ml', 'brand' => 'Pure Instinct', 'cost' => 11, 'price' => 20, 'desc' => 'Aceite de feromonas en formato roll-on para aplicar en los puntos de pulso.'],
                ['name' => 'Jabón Íntimo pH Balanceado 250ml', 'brand' => 'SEXLANDIA', 'cost' => 5, 'price' => 11, 'desc' => 'Limpiador íntimo suave sin jabón, con ácido láctico y aloe. Uso diario.'],
                ['name' => 'Toallitas Íntimas Individuales x10', 'brand' => 'SEXLANDIA', 'cost' => 3, 'price' => 7, 'desc' => 'Toallitas húmedas biodegradables en sobres individuales. Discretas para el bolso.'],
                ['name' => 'Limpiador de Juguetes Antibacterial 100ml', 'brand' => 'SEXLANDIA', 'cost' => 6, 'price' => 12, 'desc' => 'Spray higienizante sin alcohol para juguetes de silicona, TPE y ABS. Secado rápido.'],
                ['name' => 'Aceite de Masaje Sensorial 200ml', 'brand' => 'SEXLANDIA', 'cost' => 8, 'price' => 16, 'desc' => 'Aceite corporal de almendras con vitamina E y aroma cálido. Ideal para preliminares.'],
            ],
        ];

        // Pool de imágenes disponibles en public/sexlandia (excluye el logo)
        $imagePool = collect(glob(public_path('sexlandia/*.jpg')) ?: [])
            ->reject(fn ($p) => Str::contains(strtolower(basename($p)), 'logo'))
            ->values();
        $imageService = app(ProductImageService::class);
        $imgIndex = 0;

        $seq = 0;
        foreach ($productBank as $slug => $items) {
            $category = $categories->firstWhere('slug', $slug);
            if (! $category) {
                continue;
            }

            foreach ($items as $i => $tpl) {
                $seq++;
                $name = $tpl['name'];
                $sku = 'SKU-'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
                $barcode = '7591000'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);

                $product = Product::create([
                    'category_id' => $category->id,
                    'uuid' => (string) Str::uuid(),
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.$seq,
                    'description' => $tpl['desc'],
                    'sku' => $sku,
                    'sku_barcode' => $barcode,
                    'brand' => $tpl['brand'],
                    'cost' => $tpl['cost'],
                    'price' => $tpl['price'],
                    'unit_type' => 'unit',
                    'track_inventory' => true,
                    'allow_negative_stock' => false,
                    'has_variants' => false,
                    'status' => 'active',
                    'created_by' => $creator->id,
                ]);

                Inventory::create([
                    'product_id' => $product->id,
                    'stock' => rand(4, 40),
                    'reserved_stock' => 0,
                    'minimum_stock' => rand(3, 6),
                    'maximum_stock' => rand(60, 120),
                ]);

                if (isset($bulkTypesMap['unidad'])) {
                    Bulk::create([
                        'product_id' => $product->id,
                        'bulk_type_id' => $bulkTypesMap['unidad'],
                        'name' => 'Unidad',
                        'description' => 'Venta por unidad individual al detal',
                        'quantity' => 1.00,
                        'purchase_price' => $tpl['cost'],
                        'sale_price' => $tpl['price'],
                        'sku' => $product->sku.'-UND',
                        'sku_barcode' => $product->sku_barcode.'1',
                        'is_default' => true,
                        'is_active' => true,
                    ]);
                }

                if (isset($bulkTypesMap['caja'])) {
                    Bulk::create([
                        'product_id' => $product->id,
                        'bulk_type_id' => $bulkTypesMap['caja'],
                        'name' => 'Caja (6 Unidades)',
                        'description' => 'Caja sellada de 6 unidades (10% desc.)',
                        'quantity' => 6.00,
                        'purchase_price' => round($tpl['cost'] * 6 * 0.90, 2),
                        'sale_price' => round($tpl['price'] * 6 * 0.90, 2),
                        'sku' => $product->sku.'-CJ',
                        'sku_barcode' => $product->sku_barcode.'2',
                        'is_default' => false,
                        'is_active' => true,
                    ]);
                }

                // Imagen(es) optimizada(s) desde el pool
                if ($imagePool->isNotEmpty()) {
                    $primaryPath = $imagePool[$imgIndex % $imagePool->count()];
                    $imgIndex++;
                    $imageService->attachToProduct($product, $primaryPath, sortOrder: 0, isPrimary: true, altText: $name);

                    // El primer producto de cada categoría recibe una segunda foto
                    if ($i === 0) {
                        $secondPath = $imagePool[$imgIndex % $imagePool->count()];
                        $imgIndex++;
                        $imageService->attachToProduct($product, $secondPath, sortOrder: 1, isPrimary: false, altText: $name);
                    }
                }
            }
        }
    }
}
