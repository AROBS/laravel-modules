<?php

namespace Caffeinated\Modules;

use Caffeinated\Modules\Handlers\ModulesHandler;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('caffeinated/modules');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerServices();

		$this->registerConsoleCommands();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['modules.handler', 'modules'];
	}

	/**
	 * Register the package services.
	 *
	 * @return void
	 */
	protected function registerServices()
	{
		$this->app->bindShared('modules.handler', function ($app) {
			return new ModulesHandler($app['config']);
		});

		$this->app->bindShared('modules', function ($app) {
			return new Modules($app['modules.handler'], $app['config'],	$app['files']);
		});

		$this->app->booting(function ($app) {
			$app['modules']->register();
		});
	}

	/**
	 * Register the package console commands.
	 *
	 * @return void
	 */
	protected function registerConsoleCommands()
	{
		$this->registerMakeCommand();
		$this->registerEnableCommand();
		$this->registerDisableCommand();
		$this->registerMakeMigrationCommand();
		$this->registerMigrateCommand();
		$this->registerMigrateRefreshCommand();
		$this->registerMigrateResetCommand();
		$this->registerMigrateRollbackCommand();
		$this->registerSeedCommand();
		$this->registerListCommand();
        $this->registerControllerCommand();

		$this->commands([
			'modules.make',
			'modules.enable',
			'modules.disable',
			'modules.makeMigration',
			'modules.migrate',
			'modules.migrateRefresh',
			'modules.migrateReset',
			'modules.migrateRollback',
			'modules.seed',
			'modules.list',
            'modules.controller'
		]);
	}

	/**
	 * Register the "module:enable" console command.
	 *
	 * @return Console\ModuleEnableCommand
	 */
	protected function registerEnableCommand()
	{
		$this->app->bindShared('modules.enable', function() {
			return new Console\ModuleEnableCommand;
		});
	}

	/**
	 * Register the "module:disable" console command.
	 *
	 * @return Console\ModuleDisableCommand
	 */
	protected function registerDisableCommand()
	{
		$this->app->bindShared('modules.disable', function() {
			return new Console\ModuleDisableCommand;
		});
	}

	/**
	 * Register the "module:make" console command.
	 *
	 * @return Console\ModuleMakeCommand
	 */
	protected function registerMakeCommand()
	{
		$this->app->bindShared('modules.make', function($app) {
			$handler = new Handlers\ModuleMakeHandler($app['modules'], $app['files']);

			return new Console\ModuleMakeCommand($handler);
		});
	}

	/**
	 * Register the "module:make-migration" console command.
	 *
	 * @return Console\ModuleMakeMigrationCommand
	 */
	protected function registerMakeMigrationCommand()
	{
		$this->app->bindShared('modules.makeMigration', function($app) {
			$handler = new Handlers\ModuleMakeMigrationHandler($app['modules'], $app['files']);

			return new Console\ModuleMakeMigrationCommand($handler);
		});
	}

	/**
	 * Register the "module:migrate" console command.
	 *
	 * @return Console\ModuleMigrateCommand
	 */
	protected function registerMigrateCommand()
	{
		$this->app->bindShared('modules.migrate', function($app) {
			return new Console\ModuleMigrateCommand($app['modules']);
		});
	}

	/**
	 * Register the "module:migrate-refresh" console command.
	 *
	 * @return Console\ModuleMigrateRefreshCommand
	 */
	protected function registerMigrateRefreshCommand()
	{
		$this->app->bindShared('modules.migrateRefresh', function() {
			return new Console\ModuleMigrateRefreshCommand;
		});
	}

	/**
	 * Register the "module:migrate-reset" console command.
	 *
	 * @return Console\ModuleMigrateResetCommand
	 */
	protected function registerMigrateResetCommand()
	{
		$this->app->bindShared('modules.migrateReset', function($app) {
			return new Console\ModuleMigrateResetCommand($app['modules']);
		});
	}

	/**
	 * Register the "module:migrate-rollback" console command.
	 *
	 * @return Console\ModuleMigrateRollbackCommand
	 */
	protected function registerMigrateRollbackCommand()
	{
		$this->app->bindShared('modules.migrateRollback', function($app) {
			return new Console\ModuleMigrateRollbackCommand($app['modules']);
		});
	}

	/**
	 * Register the "module:seed" console command.
	 *
	 * @return Console\ModuleSeedCommand
	 */
	protected function registerSeedCommand()
	{
		$this->app->bindShared('modules.seed', function($app) {
			return new Console\ModuleSeedCommand($app['modules']);
		});
	}

	/**
	 * Register the "module:list" console command.
	 *
	 * @return Console\ModuleListCommand
	 */
	protected function registerListCommand()
	{
		$this->app->bindShared('modules.list', function($app) {
			return new Console\ModuleListCommand($app['modules']);
		});
	}

    /**
     * Register the "module:controller" console command.
     *
     * @return Console\ModuleControllerCommand
     */
    protected function registerControllerCommand()
    {
        $this->app->bindShared('modules.controller', function($app) {
            $handler = new Handlers\ModuleControllerHandler($app['modules'], $app['files']);

            return new Console\ModuleControllerCommand($handler);
        });
    }
}
