<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Singleton settings row for the bank/payment details shown on Checkout.
 * There is exactly one row, resolved via current() — no route or controller
 * anywhere accepts a client-supplied id for this model, so there is no way
 * for a request to select a different "account" to display.
 */
class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_holder',
        'account_number',
        'iban',
        'swift_bic',
        'payment_instructions',
        'whatsapp',
        'support_email',
    ];

    /**
     * The single active configuration. Creates an empty row on first use so
     * the admin always has something to edit — never seeded with real bank
     * data, only blank placeholders.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'bank_name' => '',
            'account_holder' => '',
            'account_number' => '',
            'iban' => '',
            'swift_bic' => '',
        ]);
    }
}
