<?php

namespace Modules\Settings\Services;

// Framework & Database
use Illuminate\Database\Eloquent\Collection;

// Models
use Modules\Settings\Models\Currency;

class CurrencyService
{
    /**
     * Obtener todas las monedas activas ordenadas por nombre.
     *
     * @return Collection<int, Currency>
     */
    public function getActiveCurrencies(): Collection
    {
        return Currency::active()->orderBy('name')->get();
    }

    /**
     * Obtener todas las monedas del catálogo.
     *
     * @return Collection<int, Currency>
     */
    public function all(): Collection
    {
        return Currency::orderBy('name')->get();
    }

    /**
     * Buscar una moneda por ID.
     *
     * @param int $id
     * @return Currency
     */
    public function find(int $id): Currency
    {
        return Currency::findOrFail($id);
    }
}
