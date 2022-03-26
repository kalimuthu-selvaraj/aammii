<?php

namespace Mesk\VelocityOverwrite\Providers;

use Illuminate\Support\ServiceProvider;

class VelocityOverwriteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadPublishableAssets();

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }


    /**
     * This method will load all publishables.
     *
     * @return boolean
     */
    private function loadPublishableAssets()
    {
        $this->publishes([
            __DIR__ . '/../../publishable/assets/' => public_path('themes/velocity/assets'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../Resources/views/shop' => resource_path('themes/velocity/views'),
        ]);
		
		$this->publishes([__DIR__ . '/../Resources/lang' => resource_path('lang/vendor/velocity')]);

        return true;
    }

}
