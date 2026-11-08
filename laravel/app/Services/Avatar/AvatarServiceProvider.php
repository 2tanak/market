<?php
namespace App\Services\Avatar;

use App\Services\Avatar\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AvatarServiceProvider  extends ServiceProvider
{
    
	protected $listen = [
    \Modules\Blog\Events\ArticleSaving::class => [
        \App\Services\Avatar\AvatarImageListener::class
    ]
    ];
	
	
	
	
     public function register()
    {
		
        // Связываем ключ фасада с созданием экземпляра класса
        $this->app->singleton('avatar.service', function ($app) {
            return new AvatarService($app->make(Request::class));
        });
    }

    /**
     * Загрузка сервисов.
     */
        /**
     * Загрузка сервисов.
     */
    public function boot(): void
    {
        // ВСТАВЛЯЕМ СЮДА:
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                \Illuminate\Support\Facades\Event::listen($event, $listener);
            }
        }
    }

}
