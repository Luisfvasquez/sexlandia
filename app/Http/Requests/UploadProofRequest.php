<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadProofRequest extends FormRequest
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
        return [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'required|string|max:100',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Ensure order is pending
                $orderId = $this->route('id');
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== 'pending') {
                    $validator->errors()->add('error', 'No puedes subir comprobantes a una orden ya procesada.');
                }
            },
        ];
    }
}
