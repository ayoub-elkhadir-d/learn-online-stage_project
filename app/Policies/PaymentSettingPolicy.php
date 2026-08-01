<?php

namespace App\Policies;

use App\Models\PaymentSetting;
use App\Models\User;

class PaymentSettingPolicy
{
    /**
     * Only admins may view the bank/payment settings form.
     */
    public function view(User $user, PaymentSetting $paymentSetting): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Only admins may update the bank/payment settings.
     */
    public function update(User $user, PaymentSetting $paymentSetting): bool
    {
        return $user->role === 'admin';
    }
}
