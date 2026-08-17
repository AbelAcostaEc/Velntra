<?php

namespace Modules\Settings\Services;

// Framework & Database
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Models
use Modules\Settings\Models\Setting;

class SettingService
{
    /**
     * Obtener el registro único de configuración del negocio.
     *
     * @return Setting
     */
    public function getSettings(): Setting
    {
        return Setting::getSettings();
    }

    /**
     * Actualizar la configuración general del negocio dentro de una transacción de BD.
     *
     * @param Setting $setting
     * @param array{
     *     company_name: string,
     *     ruc?: string|null,
     *     phone?: string|null,
     *     email?: string|null,
     *     address?: string|null,
     *     currency_id: int,
     *     tax_percentage: float|int|string
     * } $data
     * @param UploadedFile|null $logoFile
     * @param bool $removeLogo
     * @return Setting
     */
    public function update(
        Setting $setting,
        array $data,
        ?UploadedFile $logoFile = null,
        bool $removeLogo = false
    ): Setting {
        return DB::transaction(function () use ($setting, $data, $logoFile, $removeLogo) {
            $logoPath = $setting->logo;

            // Si se solicita remover el logo existente
            if ($removeLogo && $logoPath) {
                if (!str_starts_with($logoPath, 'http')) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = null;
            }

            // Si se subió un nuevo archivo de logo
            if ($logoFile) {
                // Eliminar el logo anterior si existía
                if ($setting->logo && !str_starts_with($setting->logo, 'http')) {
                    Storage::disk('public')->delete($setting->logo);
                }

                $sluggedName = Str::slug($data['company_name']) ?: 'company';
                $extension = strtolower($logoFile->getClientOriginalExtension() ?: 'png');
                $fileName = time() . '_' . rand(1000, 9999) . '_' . $sluggedName . '_logo.' . $extension;

                $logoPath = $logoFile->storeAs('settings', $fileName, 'public');
            }

            $setting->update([
                'company_name'   => trim($data['company_name']),
                'ruc'            => !empty($data['ruc']) ? trim($data['ruc']) : null,
                'phone'          => !empty($data['phone']) ? trim($data['phone']) : null,
                'email'          => !empty($data['email']) ? trim($data['email']) : null,
                'address'        => !empty($data['address']) ? trim($data['address']) : null,
                'logo'           => $logoPath,
                'currency_id'    => (int) $data['currency_id'],
                'tax_percentage' => (float) $data['tax_percentage'],
            ]);

            $setting->load('currency');

            return $setting;
        });
    }
}
