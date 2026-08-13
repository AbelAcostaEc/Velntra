<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the application locale, persist it in session and cookie, and redirect back.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, ['en', 'es'])) {
            $request->session()->put('locale', $locale);
            cookie()->queue(cookie()->forever('velntra_locale', $locale));
            app()->setLocale($locale);

            $request->session()->flash('toast', [
                'type'    => 'info',
                'title'   => $locale === 'es' ? 'Idioma actualizado' : 'Language updated',
                'message' => $locale === 'es' ? 'Español seleccionado correctamente.' : 'English selected successfully.',
            ]);
        }

        return back();
    }
}
