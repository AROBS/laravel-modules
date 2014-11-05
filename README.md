laravel-modules
===============

A Laravel 5 package for modullarity that contains Caffeinated Modules and Laracasts Commander packages


Caffeinated Modules
===================
[![Build Status](https://travis-ci.org/caffeinated/modules.svg?branch=master)](https://travis-ci.org/caffeinated/modules)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/caffeinated/modules/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/caffeinated/modules/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/caffeinated/modules/v/stable.svg)](https://packagist.org/packages/caffeinated/modules)
[![Total Downloads](https://poser.pugx.org/caffeinated/modules/downloads.svg)](https://packagist.org/packages/caffeinated/modules)
[![Latest Unstable Version](https://poser.pugx.org/caffeinated/modules/v/unstable.svg)](https://packagist.org/packages/caffeinated/modules)
[![License](https://poser.pugx.org/caffeinated/modules/license.svg)](https://packagist.org/packages/caffeinated/modules)

All features for the initial release as outlined within the [roadmap](https://github.com/caffeinated/modules/wiki/Roadmap#10-beta) have been completed. I'm simply waiting until Laravel 5.0 is officially released to tag this as stable v1.0. In the meantime I will continue to clean up the code that currently stands.

---

To learn more about the usage of this package, please refer to the full set of [documentation](https://github.com/caffeinated/modules/wiki). You will find quick installation instructions below.

---

Installation
------------
Begin by installing the package through Composer. The best way to do this is through your terminal via Composer itself:

```
composer require caffeinated/modules
```

Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

#### Service Provider
```
'Caffeinated\Modules\ModulesServiceProvider'
```

#### Facade
```
'Module' => 'Caffeinated\Modules\Facades\Module'
```

#### New Artisan Command
```
php artisan module:controller ModuleName ControllerName
```

And that's it! With your coffee in reach, start building out some awesome modules!



# Laravel Commander

This package gives you an easy way to leverage commands and domain events in your Laravel projects.

## Installation

Per usual, install Commander through Composer.

```js
"require": {
    "laracasts/commander": "~1.0"
}
```

Next, update `app/config/app.php` to include a reference to this package's service provider in the providers array.

```php
'providers' => [
    'Laracasts\Commander\CommanderServiceProvider'
]
```

## Usage

Easily, the most important piece of advice I can offer is to keep in mind that this approach isn't for everything. If you're building a simple CRUD app that does not have much business logic, then you likely don't need this. Still want to move ahead? Okay - onward!

### The Goal

Imagine that you're building an app for advertising job listings. Now, when an employer posts a new job listing, a number of things need to happen, right?
Well, don't put all that stuff into your controller! Instead, let's leverage commands, handlers, and domain events to clean up our code.

### The Controller

To begin, we can inject this package's `CommanderTrait` into your controller (or a BaseController, if you wish). This will give you a couple helper methods to manage the process of passing commands to the command bus.

```php
<?php

use Laracasts\Commander\CommanderTrait;

class JobsController extends \BaseController {

	use CommanderTrait;

	/**
	 * Publish the new job listing.
	 *
	 * @return Response
	 */
	public function store()
	{

	}

}
```

Good? Next, we'll represent this "instruction" (to post a job listing) as a command. This will be nothing more than a simple DTO.

```php
<?php

use Laracasts\Commander\CommanderTrait;
use Acme\Jobs\PostJobListingCommand;

class JobsController extends \BaseController {

	use CommanderTrait;

	/**
	 * Post the new job listing.
	 *
	 * @return Response
	 */
	public function store()
	{
        $this->execute(PostJobListingCommand::class);

		return Redirect::home();
	}
```

Notice how we are representing the user's instruction (or command) as a readable class: `PostJobListingCommand`. The `execute` method will expect the command's class path, as a string. Above, we're using the helpful `PostJobListingCommand::class` to fetch this. Alternatively, you could manually write out the path as a string.

### The Command DTO

Pretty simply, huh? We make a command to represent the instruction, and then we throw that command into a command bus.
Here's what that command might look like:

```php
<?php namespace Acme\Jobs;

class PostJobListingCommand {

    public $title;

    public $description;

    public function __construct($title, $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

}
```

> When you call the `execute` method on the `CommanderTrait`, it will automatically map the data from `Input::all()` to your command. You won't need to worry about doing that manually.

So what exactly does the command bus do? Think of it as a simple utility that will translate this command into an associated handler class that will, well, handle the command! In this case, that means delegating as needed to post the new job listing.

By default, the command bus will do a quick search and replace on the name of the command class to figure out which handler class to resolve out of the IoC container. As such:

- PostJobListingCommand => PostJobListingCommandHandler
- ArchiveJobCommand => ArchiveJobCommandHandler

Make sense? Good. Keep in mind, though, that if you prefer a different naming convention, you can override the defaults. See below.