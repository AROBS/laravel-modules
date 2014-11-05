<?php namespace Laracasts\Commander;

use ReflectionClass;
use InvalidArgumentException;
use Input, App;

trait CommanderTrait {

    /**
     * Execute the command.
     *
     * @param  string|BaseCommand $commandName
     * @param  array $input
     * @param  array $decorators
     * @return mixed
     */
    protected function execute($command, array $input = null)
    {
        if (!is_object($command))
        {
            $input = $input ?: Input::all();
            $command = new $command;
            $command->setInput($input);
        }

        $commandClass = get_class($command);
        $handler = substr_replace($commandClass, 'CommandHandler', strrpos($commandClass, 'Command'));

        if (!class_exists($handler))
        {
            $message = "Command handler [$handler] does not exist.";
            throw new HandlerNotRegisteredException($message);
        }

        return App::make($handler)->handle($command);
    }

}
