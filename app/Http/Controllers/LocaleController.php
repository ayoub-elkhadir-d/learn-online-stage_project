<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Switch the site language and return to wherever the visitor was —
     * never a redirect to a different page, only the text on the current
     * one changes. The locale comes solely from the {locale} route
     * segment (validated against the fixed supported list below), never
     * from any other client-submitted value.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::SUPPORTED, true), 404);

        $response = redirect(url()->previous(route('home')));

        $response->headers->setCookie(cookie('locale', $locale, 60 * 24 * 365));

        if (Auth::check()) {
            Auth::user()->update(['language' => $locale]);
        }

        return $response;
    }
}
