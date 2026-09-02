<?php

namespace App\Http\Requests;

use App\Models\AccountReceivable;
use App\Models\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterAbonoRequest extends FormRequest
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
            'account_receivable_id' => 'required|exists:accounts_receivable,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9@\.\-\_\s\#]+$/'],
            'payment_date' => 'required|date',
            'notes' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\.\,\;\:\-\/\(\)\¿\?\¡\!\@\#\%\&\=\+\'\"°\n\r]+$/'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                // If validation failed on basic rules, stop.
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $paymentMethod = PaymentMethod::find($this->payment_method_id);
                $account = AccountReceivable::where('client_id', $this->route('client'))->find($this->account_receivable_id);

                if (! $paymentMethod || ! $account) {
                    return;
                }

                $isTransferenciaOrPagoMovil = in_array($paymentMethod->id, [6, 7])
                    || str_contains(strtolower($paymentMethod->name), 'transferencia')
                    || str_contains(strtolower($paymentMethod->name), 'pago móvil')
                    || str_contains(strtolower($paymentMethod->name), 'pago movil');

                if (($paymentMethod->requires_reference || $isTransferenciaOrPagoMovil) && empty($this->reference)) {
                    $validator->errors()->add('error', 'La referencia es obligatoria para el método de pago seleccionado: '.$paymentMethod->name);
                }

                if ($this->amount > $account->pending_amount) {
                    $validator->errors()->add('error', 'El monto del abono no puede superar el saldo pendiente de la deuda ('.number_format($account->pending_amount, 2, ',', '.').').');
                }
            },
        ];
    }
}
