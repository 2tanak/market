<?php

namespace Modules\Auth\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Auth\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Model\Users\Model as User;

class AuthServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Auth';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'auth';

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
	
	
	public function boot(): void
    {
		
        // Сначала запускаем логику родительского провайдера nwidart
        parent::boot();

        // 2. РЕГИСТРИРУЕМ ПОЛИТИКУ ДЛЯ МОДЕЛИ ЮЗЕРА НА ЛЕТУ
        Gate::policy(User::class, UserPolicy::class);
        
       
    }
	
	
	
	
}
