<?php

namespace App\Http\Controllers;

use App\Actions\Product\CreateProductAction;
use App\Actions\Product\UpdateProductAction;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\BulkType;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Sanitizar el input de búsqueda por seguridad (letras, números y caracteres simples)
        $searchSanitized = $search ? preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\-\.\_\@]/u', '', $search) : null;

        $products = Product::with(['category', 'inventory'])
            ->when($searchSanitized, function ($query) use ($searchSanitized) {
                $searchTerm = '%'.$searchSanitized.'%';
                $query->where(function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', $searchTerm)
                        ->orWhere('sku', 'like', $searchTerm)
                        ->orWhere('sku_barcode', 'like', $searchTerm);
                });
            })
            ->paginate(10)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $bulkTypes = BulkType::all();

        return view('admin.products.create', compact('categories', 'bulkTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, CreateProductAction $createProduct)
    {
        try {
            $createProduct->handle(
                $request->validated(),
                $request->file('images'),
                $request->input('presentations')
            );

            return redirect()->route('admin.products.index')
                ->with('success', 'Producto creado/restaurado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Hubo un problema al guardar el producto: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $updateProduct)
    {
        try {
            $updateProduct->handle(
                $product,
                $request->validated(),
                $request->file('images')
            );

            return redirect()->route('admin.products.index')
                ->with('success', 'Producto actualizado con éxito.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->withInput()->withErrors([
                'error' => 'Hubo un problema al actualizar el producto: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Elimina bulks, imágenes (archivos + BD) e inventario antes del soft-delete.
     */
    public function destroy(Product $product)
    {
        try {
            // DB::transaction allows us to skip DB::beginTransaction() etc manually,
            // but for simplicity we will just let the model handle soft deletes
            // and keep the same logic. Let's use it as it was but with route model binding.
            DB::beginTransaction();

            $product->bulks()->delete();
            $this->deleteAllProductImages($product);
            $product->inventory()->delete();
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Producto eliminado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return back()->withErrors([
                'error' => 'Hubo un problema al eliminar el producto.',
            ]);
        }
    }

    /**
     * Elimina TODAS las imágenes de un producto (archivos del disco + registros BD).
     */
    private function deleteAllProductImages(Product $product): void
    {
        $imageService = app(ProductImageService::class);

        foreach ($product->images as $image) {
            $imageService->deleteForImage($image);
            $image->delete();
        }

        // Eliminar el directorio de imágenes del producto si existe
        $productDir = 'products/'.$product->id;
        if (Storage::disk('public')->exists($productDir)) {
            Storage::disk('public')->deleteDirectory($productDir);
        }
    }

    public function destroyImage($imageId)
    {
        // Importamos el modelo
        $image = Image::findOrFail($imageId);

        // Elimina las 3 renditions (full + md_ + thumb_) del disco
        app(ProductImageService::class)->deleteForImage($image);

        $image->delete();

        return back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function forzarActualizacionDolar()
    {
        Artisan::call('exchange:update-usd');

        return back()->with('success', 'La tasa del dólar ha sido actualizada.');
    }
}
