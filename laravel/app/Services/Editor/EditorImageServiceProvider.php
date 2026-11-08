<?php

namespace App\Services\Editor;

use Illuminate\Support\ServiceProvider;

class EditorImageServiceProvider extends ServiceProvider
{
	
	protected $listen = [
    \Modules\Blog\Events\ArticleSaving::class => [
        \App\Services\Editor\EditorImageListener::class
    ]
    ];
	
	
    public function register(): void
    {
        $this->app->singleton('editor.image.service', function () {
            return new EditorImageService();
        });
    }

    public function boot(): void
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('EditorImage', EditorImage::class);
		
		   foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                \Illuminate\Support\Facades\Event::listen($event, $listener);
            }
        }
		
		
		
		
    }
}
