<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Assuming route binds product ID or Product model
        // If route binding is used: $productId = $this->product->id;
        // If ID is used: $productId = $this->route('product');
        // Let's get ID safely. In ProductController it is $id from string. So route param is 'product' (from resource).
        $product = $this->route('product'); // If route model binding isn't fully working, this could be an ID.
        $productId = $product instanceof Product ? $product->id : $product;

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\-\/\(\)\&\%]+$/'],
            'sku' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9\-\_]+$/', 'unique:products,sku,'.$productId],
            'sku_barcode' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9\-]+$/', 'unique:products,sku_barcode,'.$productId],
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'unit_type' => 'required|in:unit,gram',
            'brand' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\-\&\']+$/'],
            'description' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\;\:\-\/\(\)\¿\?\¡\!\@\#\%\&\=\+\'\"°\n\r]+$/'],
            'minimum_stock' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }
}
