<?php

namespace App\Http\Requests\Admin;

use App\Models\PaymentSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingRequest extends FormRequest
{
    /**
     * Authorization is layered: the 'auth'+'admin' route middleware already
     * blocks non-admins from reaching this request, and the controller also
     * runs $this->authorize('update', ...) against PaymentSettingPolicy.
     * This check is a third, independent layer — if any of the others were
     * ever misconfigured, this still refuses the request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->role === 'admin'
            && $this->user()->can('update', PaymentSetting::current());
    }

    /**
     * Only these exact keys are ever accepted — anything else in the
     * request body is silently dropped by validated(), never reaching
     * the model's fill()/update().
     */
    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:255'],
            'account_holder' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
            'swift_bic' => ['required', 'string', 'max:20'],
            'payment_instructions' => ['nullable', 'string', 'max:2000'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'support_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
