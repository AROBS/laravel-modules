<?php

namespace Caffeinated\Modules\Handlers;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleMakeMigrationHandler
{
	/**
	 * @var Modules
	 */
	protected $module;

	/**
	 * @var Filesystem
	 */
	protected $finder;

	/**
	 * @var Command
	 */
	protected $console;

	/**
	 * @var string
	 */
	protected $moduleName;

	/**
	 * @var string
	 */
	protected $table;

    /**
     * @var string
     */
    protected $create;

    /**
     * @var string
     */
    protected $disable_fk;

	/**
	 * @var string
	 */
	protected $migrationName;

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * Constructor method.
	 *
	 * @return void
	 */
	public function __construct(Modules $module, Filesystem $finder)
	{
		$this->module = $module;
		$this->finder = $finder;
	}

	/**
	 * Fire off the handler.
	 *
	 * @param Command $console
	 * @param string $slug
	 * @return bool
	 */
	public function fire(Command $console, $slug, $migrationName, $table, $create, $disable_fk)
	{
		$this->console       = $console;
		$this->moduleName    = Str::studly($slug);
        $this->migrationName = $migrationName;
		$this->table         = $table ? : strtolower($table);
        $this->create        = $create ? true : false;
        $this->disable_fk    = $disable_fk ? true : false;
		$this->className     = studly_case($this->migrationName);

		if ($this->module->has($this->moduleName)) {
			$this->makeFile();

			$this->console->info("Created Module Migration: [$this->moduleName] " . $this->getFilename());

			return $this->console->call('dump-autoload');
		}

		return $this->console->info("Module [$this->moduleName] does not exist.");
	}

	/**
	 * Create new migration file.
	 *
	 * @return string
	 */
	protected function makeFile()
	{
		return $this->finder->put($this->getDestinationFile(), $this->getStubContent());
	}

	/**
	 * Get file destination.
	 *
	 * @return string
	 */
	protected function getDestinationFile()
	{
		return $this->getPath() . $this->formatContent($this->getFilename());
	}

	/**
	 * Get module migration path.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		$path = $this->module->getModulePath($this->moduleName);

		return $path . 'Database/Migrations/';
	}

	/**
	 * Get migration filename.
	 *
	 * @return string
	 */
	protected function getFilename()
	{
		return date("Y_m_d_His") . '_' . $this->migrationName . '.php';
	}

	/**
	 * Get stub content.
	 *
	 * @return string
	 */
	protected function getStubContent()
	{
		return $this->create ?
            ($this->disable_fk ?
                $this->formatContent($this->finder->get(__DIR__.'/../Console/stubs/migrationcreatefk.stub')) :
                $this->formatContent($this->finder->get(__DIR__.'/../Console/stubs/migrationcreate.stub'))
            ) :
            $this->formatContent($this->finder->get(__DIR__.'/../Console/stubs/migration.stub'));
	}

	/**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{migrationName}}', '{{table}}'],
			[$this->className, $this->table],
			$content
		);
	}
}