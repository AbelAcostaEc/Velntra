<?php

namespace Modules\Settings\Livewire\Settings;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

// Models
use Modules\Settings\Models\Setting;

// Services
use Modules\Settings\Services\CurrencyService;
use Modules\Settings\Services\SettingService;

#[Layout('layouts.app')]
class SettingIndex extends Component
{
    use WithFileUploads;

    /**
     * Nombre o razón social del negocio.
     */
    public string $company_name = '';

    /**
     * Número de identificación tributaria (RUC, NIT, CIF, VAT).
     */
    public ?string $ruc = '';

    /**
     * Teléfono o celular de contacto.
     */
    public ?string $phone = '';

    /**
     * Correo electrónico comercial.
     */
    public ?string $email = '';

    /**
     * Dirección física de la matriz o local.
     */
    public ?string $address = '';

    /**
     * Ruta del archivo de logotipo guardado en el servidor.
     */
    public ?string $logo = null;

    /**
     * Archivo temporal cargado para nuevo logotipo.
     */
    public $logoFile = null;

    /**
     * Indicador si se solicitó eliminar el logotipo existente.
     */
    public bool $removeLogoFlag = false;

    /**
     * ID de la moneda principal del negocio.
     */
    public ?int $currency_id = null;

    /**
     * Porcentaje de impuesto por defecto aplicado a las ventas (IVA).
     */
    public $tax_percentage = '15.00';

    /**
     * Cargar la configuración actual al inicializar el componente.
     */
    public function mount(SettingService $settingService): void
    {
        $this->authorize('viewAny', Setting::class);

        $setting = $settingService->getSettings();

        $this->company_name   = $setting->company_name;
        $this->ruc            = $setting->ruc ?? '';
        $this->phone          = $setting->phone ?? '';
        $this->email          = $setting->email ?? '';
        $this->address        = $setting->address ?? '';
        $this->logo           = $setting->logo;
        $this->currency_id    = $setting->currency_id;
        $this->tax_percentage = (string) $setting->tax_percentage;
    }

    /**
     * Reglas de validación para el formulario de configuración.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'company_name'   => 'required|string|max:255',
            'ruc'            => 'nullable|string|max:30',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:1000',
            'currency_id'    => 'required|integer|exists:currencies,id',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'logoFile'       => 'nullable|image|max:2048',
        ];
    }

    /**
     * Nombres de atributos personalizados para los mensajes de error.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'company_name'   => __t('field_company_name', 'settings'),
            'ruc'            => __t('field_ruc', 'settings'),
            'phone'          => __t('field_phone', 'settings'),
            'email'          => __t('field_email', 'settings'),
            'address'        => __t('field_address', 'settings'),
            'currency_id'    => __t('field_currency', 'settings'),
            'tax_percentage' => __t('field_tax_percentage', 'settings'),
            'logoFile'       => __t('field_logo', 'settings'),
        ];
    }

    /**
     * Marcar el logotipo para ser eliminado al guardar.
     */
    public function removeLogo(): void
    {
        $this->logo = null;
        $this->logoFile = null;
        $this->removeLogoFlag = true;
    }

    /**
     * Guardar los cambios de configuración general.
     */
    public function save(SettingService $settingService): void
    {
        $this->authorize('update', Setting::class);

        $this->validate();

        $setting = $settingService->getSettings();

        $settingService->update(
            $setting,
            [
                'company_name'   => $this->company_name,
                'ruc'            => $this->ruc,
                'phone'          => $this->phone,
                'email'          => $this->email,
                'address'        => $this->address,
                'currency_id'    => $this->currency_id,
                'tax_percentage' => $this->tax_percentage,
            ],
            $this->logoFile,
            $this->removeLogoFlag
        );

        $fresh = $setting->fresh();
        $this->logo = $fresh->logo;
        $this->logoFile = null;
        $this->removeLogoFlag = false;

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('settings_updated', 'settings'),
        ]);
    }

    /**
     * Renderizar la vista de configuración general.
     */
    public function render(CurrencyService $currencyService): View
    {
        $this->authorize('viewAny', Setting::class);

        $currencies = $currencyService->getActiveCurrencies();

        $selectedCurrency = $this->currency_id
            ? $currencies->firstWhere('id', $this->currency_id)
            : null;

        return view('settings::livewire.settings.setting-index', [
            'currencies'       => $currencies,
            'selectedCurrency' => $selectedCurrency,
        ]);
    }
}
