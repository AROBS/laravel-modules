<?php

namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Caffeinated\Modules\Traits\MigrationTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateRollbackCommand extends Command
{
	use MigrationTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:migrate-rollback';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Rollback the last database migrations for a specific or all modules';

	/**
	 * @var Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Modules $module)
	{
		parent::__construct();

		$this->module   = $module;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$module = $this->argument('module');

		if ($module) {
			return $this->rollback($module);
		} else {
			foreach ($this->module->all() as $module) {
				$this->rollback($module['slug']);
			}
		}
	}

	/**
	 * Run the migration rollback for the specified module.
	 *
	 * @param string $slug
	 * @return mixed
	 */
	protected function rollback($slug)
	{
		$moduleName = Str::studly($slug);

		$this->requireMigrations($moduleName);

		$this->call('migrate:rollback', [
			'--database' => $this->option('database'),
			'--force'    => $this->option('force'),
			'--pretend'  => $this->option('pretend'),
		]);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [['module', InputArgument::OPTIONAL, 'Module slug.']];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
			['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.']
		];
	}
}