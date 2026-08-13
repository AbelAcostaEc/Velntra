<?php

namespace Modules\Administration\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Modules\Administration\Models\User;
use Modules\Administration\Policies\UserPolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;

class AdministrationServiceProvider extends ModuleServiceProvider
{
    /**
     * Policies to register for the module.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        User::class => UserPolicy::class,
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
     * Register module policies.
     */
    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
    /**
     * The name of the module.
     */
    protected string $name = 'Administration';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'administration';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

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
