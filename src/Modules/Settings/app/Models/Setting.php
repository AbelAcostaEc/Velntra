<?php

namespace Modules\Settings\Models;

// Framework & Database
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Settings\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
        'ruc',
        'phone',
        'email',
        'address',
        'logo',
        'currency_id',
        'tax_percentage',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'currency_id'    => 'integer',
            'tax_percentage' => 'decimal:2',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }

    /**
     * Currency associated with the business settings.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Accessor for full logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Singleton accessor: get the single configuration record or instantiate default.
     */
    public static function getSettings(): self
    {
        $setting = static::with('currency')->first();

        if (! $setting) {
            $defaultCurrency = Currency::where('code', 'USD')->first() ?? Currency::first();

            $setting = static::create([
                'company_name'   => 'Velntra Store',
                'ruc'            => '1790012345001',
                'phone'          => '0999999999',
                'email'          => 'contacto@velntra.test',
                'address'        => 'Av. Principal 123 y Secundaria',
                'logo'           => null,
                'currency_id'    => $defaultCurrency?->id,
                'tax_percentage' => 15.00,
            ]);

            $setting->load('currency');
        }

        return $setting;
    }
}
