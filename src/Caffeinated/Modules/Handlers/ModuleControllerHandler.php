<?php

namespace Caffeinated\Modules\Handlers;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleControllerHandler
{
	/**
	 * Controller stub used to populate defined file.
	 *
	 * @var string
	 */
	protected $stubs = ['controller.stub'];

	/**
	 * @var Modules
	 */
	protected $modules;

	/**
	 * @var Filesystem
	 */
	protected $finder;

    /**
     * Module controller file to be created.
     *
     * @var array
     */
    protected $files = [
        'Http/Controllers/{{name}}Controller.php',
    ];

	/**
     * Module slug
	 * @var string
	 */
	protected $moduleSlug;

	/**
     * Module name
	 * @var string
	 */
	protected $moduleName;

    /**
     * Controller slug
     * @var string
     */
    protected $slug;

    /**
     * Controller name
     * @var string
     */
    protected $name;

	/**
	 * Constructor method.
	 *
	 * @param Modules $module
	 * @param Filesystem $finder
	 */
	public function __construct(Modules $modules, Filesystem $finder)
	{
		$this->modules = $modules;
		$this->finder  = $finder;
	}

	/**
	 * Fire off the handler.
	 *
	 * @param Command $console
	 * @param string $slug
     * @param string $controller
	 * @return bool
	 */
	public function fire(Command $console, $moduleSlug, $controllerSlug)
	{
		$this->console    = $console;
		$this->moduleSlug = $moduleSlug;
		$this->moduleName = Str::studly($moduleSlug);
        $this->slug       = $controllerSlug;
        $this->name       = Str::studly($controllerSlug);

		if (!$this->modules->has($this->moduleSlug)) {
			$console->comment("Module [{$this->moduleName}] doesn't exist.");

			return false;
		}

        // @TODO: check if controller already exists

		$this->generate($console);
	}

	/**
	 * Generate module controller file.
	 *
	 * @param Command $console
	 * @return bool
	 */
	public function generate(Command $console)
	{
		$this->generateController();

		$console->info("Controller [{$this->name}] has been created successfully in module [{$this->moduleName}].");

		return true;
	}

	/**
	 * Generate defined module files.
	 *
	 * @return void
	 */
	protected function generateController()
	{
		foreach ($this->files as $key => $file) {
			$file = $this->formatContent($file);
			
			$this->makeFile($key, $file);
		}
	}

	/**
	 * Create module file.
	 *
	 * @param int $key
	 * @param string $file
	 * @return string
	 */
	protected function makeFile($key, $file)
	{
		return $this->finder->put($this->getDestinationFile($file), $this->getStubContent($key));
	}

	/**
	 * Get the path to the module.
	 *
	 * @param string $slug
	 * @return string
	 */
	protected function getModulePath($slug = null)
	{
		if ($slug)
			return $this->modules->getModulePath($slug);

		return $this->modules->getPath();
	}

	/**
	 * Get destination file.
	 *
	 * @param string $file
	 * @return string
	 */
	protected function getDestinationFile($file)
	{
		return $this->getModulePath($this->moduleSlug).$this->formatContent($file);
	}

	/**
	 * Get stub content by key.
	 *
	 * @param int $key
	 * @return string
	 */
	protected function getStubContent($key)
	{
		return $this->formatContent($this->finder->get(__DIR__.'/../Console/stubs/'.$this->stubs[$key]));
	}

	/**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{slug}}', '{{name}}', '{{moduleSlug}}', '{{moduleName}}', '{{namespace}}'],
			[$this->slug, str_replace(array('Controller', 'controller'), '', $this->name), $this->moduleSlug, $this->moduleName, $this->modules->getNamespace()],
			$content
		);
	}
}