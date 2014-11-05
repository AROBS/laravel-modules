<?php

namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Handlers\ModuleMakeMigrationHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeMigrationCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:make-migration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module migration file';

	/**
	 * @var ModuleMakeMigrationHandler
	 */
	protected $handler;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ModuleMakeMigrationHandler $handler)
	{
		parent::__construct();

		$this->handler = $handler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		return $this->handler->fire($this, $this->argument('module'), $this->argument('name'), $this->option('table'), $this->option('create'), $this->option('disable-fk'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['module', InputArgument::REQUIRED, 'Module slug.'],
			['name', InputArgument::REQUIRED, 'Migration name.']
		];
	}

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table', null, InputOption::VALUE_OPTIONAL, 'Migration table.', ''],
            ['create', null, InputOption::VALUE_NONE, 'Migration create table.'],
            ['disable-fk', null, InputOption::VALUE_NONE, 'Migration create table with disabled foreign key check.']
        ];
    }
}