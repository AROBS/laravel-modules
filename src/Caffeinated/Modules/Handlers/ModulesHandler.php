<?php

namespace Caffeinated\Modules\Handlers;

use Countable;
use Caffeinated\Modules\Exceptions\FileMissingException;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;

class ModulesHandler implements Countable
{
	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var String
	 */
	protected $path;

    /**
     * @var All modules
     */
    protected $all;

    /**
     * @var All slugs
     */
    protected $slugs;

	/**
	 * Constructor method.
	 *
	 * @param Repository $config
	 */
	public function __construct(Repository $config)
	{
		$this->config = $config;
	}

	/**
	 * Get all modules.
	 *
	 * @return array
	 */
	public function all()
	{
        if ($this->all) {
            return $this->all;
        }

        // Get modules from config/modules.php file
        $config = $this->getModulesConfig();

        // Set the modules
        $this->all = $config;

        // Build slugs
        $this->slugs();

        return $this->all;
	}

    /**
     * Get all modules slugs
     *
     * @return mixed
     * @throws FileMissingException
     */
    public function slugs()
    {
        if ($this->slugs) {
            return $this->slugs;
        }

        // Get modules from config/modules.php file
        $config = $this->all ? : $this->getModulesConfig();

        $this->slugs = array_map(function($item) {
            return $item['slug'];
        }, array_filter($config, function($arr) {
            return array_key_exists('slug', $arr);
        }));

        return $this->slugs;

    }

	/**
	 * Check if given module exists.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function has($slug)
	{
		return in_array($slug, $this->slugs());
	}

	/**
	 * Return count of all modules.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->all());
	}

	/**
	 * Gets module path.
	 *
	 * @return string
	 */
	public function getPath()
	{
        return $this->path ?: $this->config->get('modules::paths.modules') ?: app_path('Modules');
    }

	/**
	 * Sets module path.
	 *
	 * @param string $path
	 * @return self
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Gets the path of specified module.
	 *
	 * @param string $module
	 * @param bool $allowNotExists
	 * @return null|string
	 */
	public function getModulePath($module, $allowNotExists = false)
	{
		$module = Str::studly($module);

		if ($this->has($module) and $allowNotExists === false) {
            return null;
        }

		return $this->getPath() . "/{$module}/";
	}

	/**
	 * Get a module property value.
	 *
	 * @param string $property
	 * @param null|String $default
	 * @return mixed
	 */
	public function getProperty($property, $default = null)
	{
		list($module, $key) = explode('::', $property);

		return array_get(array_get($this->all(), $module, []), $key, $default);
	}

	/**
	 * Set a module property value.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @return bool
	 */
	public function setProperty($property, $value)
	{
        // Not available
        return false;
	}

    /**
     * Get module properties as an array.
     * Legacy function.
     *
     * @param string $module
     * @return array|mixed
     */
    public function getModuleContents($module)
    {
        return array_get($this->all(), $module, []);
    }

	/**
	 * Get module properties as an array.
     * Legacy function.
	 *
	 * @param string $module
	 * @return array|mixed
	 */
	public function getJsonContents($module)
	{
        return $this->getModuleContents($module);
	}

	/**
	 * Set module JSON content property value.
     * Legacy function.
	 *
	 * @param $module
	 * @param array $content
	 * @return int
	 */
	public function setJsonContents($module, array $content)
	{
        // Not implemented
		return 0;
	}

	/**
	 * Get path of module JSON file.
     * Legacy function.
	 *
	 * @param string $module
	 * @return string
	 */
	public function getJsonPath($module)
	{
		return $this->getModulePath($module) . '/module.json';
	}

	/**
	 * Enables the specified module.
     * Legacy function.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function enable($slug)
	{
        // Not implemented
		return false;
	}

	/**
	 * Disables the specified module.
     * Legacy function
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function disable($slug)
	{
        // Not implemented
		return false;
	}


    /**
     * Get modules from config/modules.php file
     *
     * @return mixed
     * @throws FileMissingException
     */
    public function getModulesConfig()
    {
        $config = $this->config->get('modules.modules');

        if (!$config) {
            $message = "No valid config/modules.php file found.";

            throw new FileMissingException($message);
        }

        return $config;
    }
}