<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We can assume auth middleware handles it, or add role logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $trashedProduct = Product::onlyTrashed()
            ->where(function ($query) {
                $query->where('sku_barcode', $this->sku_barcode)
                    ->orWhere('sku', $this->sku);
            })
            ->first();

        $skuUniqueRule = ['required_without:sku_barcode', 'string', 'regex:/^[a-zA-Z0-9\-\_]+$/', 'unique:products,sku'.($trashedProduct ? ','.$trashedProduct->id : '')];
        $skuBarcodeUniqueRule = ['required_without:sku', 'string', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:products,sku_barcode'.($trashedProduct ? ','.$trashedProduct->id : '')];

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\-\/\(\)\&\%]+$/'],
            'sku' => $skuUniqueRule,
            'sku_barcode' => $skuBarcodeUniqueRule,
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'unit_type' => 'required|in:unit,gram',
            'brand' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\-\&\']+$/'],
            'description' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\;\:\-\/\(\)\¿\?\¡\!\@\#\%\&\=\+\'\"°\n\r]+$/'],
            'minimum_stock' => 'nullable|numeric|min:0',
            'presentations' => 'nullable|array',
            'presentations.*.bulk_type_id' => 'required|exists:bulk_types,id',
            'presentations.*.name' => ['required', 'string', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\-\/\(\)\&\%]+$/'],
            'presentations.*.quantity' => 'required|numeric|min:0.01',
            'presentations.*.purchase_price' => 'required|numeric|min:0',
            'presentations.*.sale_price' => 'required|numeric|min:0',
            'presentations.*.sku' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-\_]+$/'],
            'presentations.*.sku_barcode' => ['required', 'string', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es requerido.',
            'sku.required' => 'El SKU del producto es requerido.',
            'sku_barcode.required' => 'El código de barras del producto es requerido.',
            'sku.unique' => 'El SKU ya existe en la base de datos.',
            'sku_barcode.unique' => 'El código de barras ya existe en la base de datos.',
            'presentations.*.name.required' => 'El nombre de la presentación es requerido.',
            'presentations.*.type.required' => 'El tipo de presentación es requerido.',
            'presentations.*.quantity.required' => 'La cantidad de la presentación es requerida.',
            'presentations.*.purchase_price.required' => 'El precio de compra de la presentación es requerido.',
            'presentations.*.sale_price.required' => 'El precio de venta de la presentación es requerido.',
            'presentations.*.sku.unique' => 'El SKU de la presentación ya existe en la base de datos.',
            'presentations.*.sku_barcode.unique' => 'El código de barras de la presentación ya existe en la base de datos.',
        ];
    }
}
