<?php

namespace Vshapovalov\Crud;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Vshapovalov\Crud\Commands\InstallCommand;
use Vshapovalov\Crud\Facades\Crud as CrudFacade;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crud');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	    $loader = AliasLoader::getInstance();
	    $loader->alias('Crud', CrudFacade::class);

    	$this->app->singleton('crud', function () {
    		return new Crud();
	    });

    	$this->loadHelpers();

    	if ($this->app->runningInConsole()) {

		    $this->registerPublishableResources();
		    $this->registerConsoleCommands();
	    }
    }

    function registerConsoleCommands(){
    	$this->commands(InstallCommand::class);
    }

	/**
	 * Register the publishable files.
	 */
	private function registerPublishableResources()
	{
		$publishablePath = dirname(__DIR__).'/publishable';
		$assetsPath = '/vendor/vshapovalov/crud/assets';

		$publishable = [
			'crud_assets' => [
				"{$publishablePath}/assets/" => public_path($assetsPath),
			],
			'crud_migrations' => [
				"{$publishablePath}/database/migrations/" => database_path('migrations'),
			],
			'crud_seeds' => [
				"{$publishablePath}/database/seeds/" => database_path('seeds'),
			],
			'crud_scripts' => [
				"{$publishablePath}/database/scripts/" => database_path('scripts'),
			],
			'crud_config' => [
				"{$publishablePath}/config/cruds.php" => config_path('cruds.php'),
			],
		];

		foreach ($publishable as $group => $paths) {
			$this->publishes($paths, $group);
		}
	}

	public function provides()
	{
		return [
			Crud::class,
		];
	}

	/**
	 * Load helpers.
	 */
	protected function loadHelpers()
	{
		foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
			require_once $filename;
		}
	}


}
