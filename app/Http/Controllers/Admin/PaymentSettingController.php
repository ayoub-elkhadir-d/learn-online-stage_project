<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePaymentSettingRequest;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    /**
     * The route never accepts a client-supplied id — the single row is
     * always resolved server-side via PaymentSetting::current().
     */
    public function edit()
    {
        $setting = PaymentSetting::current();

        $this->authorize('view', $setting);

        return view('admin.payments.bank-settings', compact('setting'));
    }

    public function update(UpdatePaymentSettingRequest $request)
    {
        $setting = PaymentSetting::current();

        $this->authorize('update', $setting);

        // validated() only ever contains the whitelisted keys from the
        // FormRequest's rules() — anything else submitted is discarded here,
        // and $fillable on the model is a second guard against mass
        // assignment even if this ever changed to $request->all().
        $setting->update($request->validated());

        return redirect()
            ->route('admin.payments.bank-settings.edit')
            ->with('success', 'Bank information updated.');
    }
}
