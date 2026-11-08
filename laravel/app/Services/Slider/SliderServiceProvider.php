<?php

namespace App\Services\Slider;

use App\Services\Slider\SliderService;
use Illuminate\Support\ServiceProvider;

class SliderServiceProvider extends ServiceProvider
{
	
	protected $listen = [
    \Modules\Blog\Events\ArticleSaving::class => [
        \App\Services\Slider\SliderListener::class
    ]
    ];
	
	
	
	
    public function register(): void
    {
        // Регистрируем синглтон под именем фасада
        $this->app->singleton('slider.service', function ($app) {
            return new SliderService($app->make('request'));
        });
    }

    public function boot(): void
    {
          foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                \Illuminate\Support\Facades\Event::listen($event, $listener);
            }
        }
    }
}
