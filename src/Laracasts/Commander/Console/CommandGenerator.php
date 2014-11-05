<?php namespace Laracasts\Commander\Console;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;

class CommandGenerator {

    /**
     * The Filesystem instance.
     *
     * @var Filesystem
     */
    protected $file;

    /**
     * The Mustache_Engine instance.
     *
     * @var Mustache_Engine
     */
    protected $mustache;

    /**
     * Creat a new CommandGenerator instance.
     *
     * @param Filesystem $file
     * @param Mustache_Engine $mustache
     */
    public function __construct(Filesystem $file, Mustache_Engine $mustache)
    {
        $this->file = $file;
        $this->mustache = $mustache;

        $this->mustache->addHelper('case', [
            'lower' => function($value) { return strtolower((string) $value); },
            'upper' => function($value) { return strtoupper((string) $value); },
            'ucfirst' => function($value) { return ucfirst((string) $value); },
        ]);
    }

    /**
     * Generate the files for a new command and handler.
     * 
     * @param CommandInput $input
     * @param $template
     * @param $destination
     */
    public function make(CommandInput $input, $template, $destination)
    {
        $template = $this->file->get($template);

        $stub = $this->mustache->render($template, $input);

        $this->file->put($destination, $stub);
    }

}
