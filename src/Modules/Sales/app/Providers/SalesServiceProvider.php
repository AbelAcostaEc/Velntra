<?php

namespace Modules\Sales\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Sales\Models\Sale;
use Modules\Sales\Policies\SalePolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;

class SalesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Sales';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'sales';

    /**
     * Provider classes to register.
     *
     * @var list<class-string>
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * The policy mappings for the module.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Sale::class => SalePolicy::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        parent::boot();
        $this->registerPolicies();
    }

    /**
     * Register module policies with Gate.
     */
    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Register translations for the module.
     */
    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }
}
