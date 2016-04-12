# ViewPages

Support view/rendering of Laravel pages and templates from a database.

Can be used for content management, admin interfaces (e.g. using AdminLTE or other
front end frameworks), etc.

Supports loading both Blade and Twig templates using the same interface as existing
view loading, for example:

```php
    return View::make("dashboard.sysadmin")
        ->with('page_title', 'System Administrator Dashboard')
        ->with('tasks', $tasks);
```

## Rationale

The lack of ability to have database backed views, templates, and layouts is one of the
missing features that prevents Laravel from being used to create a truly dynamic CMS.  This
package aims to fix that.

TerrePorter partially solves this issue with his StringBladeCompiler package.  His package
was originally based on Flynsarmy/laravel-db-blade-compiler which did support taking a blade
from a model object but is no longer maintained.

# Installation

Add the package using composer from the command line:

```
    composer require delatbabel/viewpages
```

Alternatively, pull the package in manually by adding these lines to your composer.json file:

```
    "require": {
        "delatbabel/viewpages": "~1.0"
    },
```

Once that is done, run the composer update command:

```
    composer update
```

## Register Service Provider

After composer update completes, remove this line from your config/app.php file in the 'providers'
array (or comment it out):

```
    Illuminate\View\ViewServiceProvider::class
```

Replace it with this line:

```
    Delatbabel\ViewPages\ViewPagesServiceProvider::class,
```

## Incorporate and Run the Migrations

Finally, incorporate and run the migration scripts to create the database tables as follows:

```
    php artisan vendor:publish --provider='Delatbabel\ViewPages\ViewPagesServiceProvider' --force
    php artisan migrate
```

Prior to running the migration scripts you may want to alter the scripts themselves, or
alter the base templates contained in database/seeds/examples.  The ones provided are a
few examples based on [AdminLTE](https://almsaeedstudio.com/).

# How to Use This Package

## Creating Views

* Install and run the migrations as per the above.
* Populate the vpages table with your templates.  They do not have to look any different
  to standard Laravel blade templates -- see the section below on **Blade Compilation**.
* In addition to the **content** column which should contain the template or page content,
  populate the following columns:
* **pagekey** -- page lookup key.
* **url** -- page lookup URL, may be useful when you want to look up page content by URL.
* **name** -- a descriptive name of the page, eg "main website home page".
* **description** -- a longer description of the page.
* **pagetype** -- either **blade.php** or **twig** depending on the view language.

The important thing here is the **pagekey**.  This basically takes the place of the view
name used to find the view in the existing Laravel View facade.  So, for example, if you would
normally use `View::make("dashboard.sysadmin");` to find the view, you would normally store
the view on disk in `resources/views/dashboard/sysadmin.blade.php`.  Instead you would store the
view in the vpages table as follows:

* `pagekey` = `"dashboard.sysadmin"`
* `url` = `"dashboard/sysadmin"`
* `name` = whatever you like as a name, e.g. "sysadmin dashboard"
* `description` = whatever you like, e.g. "My system admin dashboard"
* `content` = exactly the blade content that you would normally store on disk
* `pagetype` = `blade.php`.

## Creating Blade Templates

You can still use templates (layouts) as you normally would in Laravel.  For example, your
template can contain this:

```html
<html>
<head><title>{{ $page_title }}</title></head>
<body>
@yield('body)
</body>
</html>
```

The body can then contain this:

```html
@extends('layouts.main')

@section('body)
<p>Body text goes here</p>
@endsection
```

Store the template in the vpages table with pagekey = 'layouts.main' and it will automatically
be found and extended by your body view.

See [Template Inheritance](https://laravel.com/docs/5.1/blade#template-inheritance) for more details.

### Variant Content

These are website dependent data blocks, stored in the vobjects table and retrieved using the
VojbecService service which can be injected into a page using
[Laravel Service Injection](https://laravel.com/docs/5.1/blade#service-injection).

Example:

```html
@inject('objects', 'Delatbabel\ViewPages\Services\VobjectService')

<!-- Using the regular make method -->
<title> {{ $objects->make('page_title') }} </title>

<!-- Using a magic getter -->
<title> {{ $objects->page_title }} </title>
```

## Twig Views and Templates

This package now supports views and templates using the [Twig](http://twig.sensiolabs.org/) templating
language as well as blade templates, via the [TwigBridge](https://github.com/rcrowe/TwigBridge) class.

Store these in the database tables alongside your blade templates using `pagetype` = "twig".

## Using Templates

Once the templates are created, you can use them just like any other view file, e.g.

```php
    return View::make("dashboard.sysadmin")
        ->with('page_title', 'System Administrator Dashboard')
        ->with('tasks', $tasks);
```

The underlying Factory class will try to find the view by doing the following steps in
order until a hit is found:

* Look in the vpages table for a vpage with pagekey = dashboard.sysadmin.
* Look in the vpages table for a vpage with url = dashboard.sysadmin.
* Look on disk for a view called resources/views/sysadmin/dashboard.blade.php
* Look on disk for a view called resources/views/sysadmin/dashboard.twig
* Look in the vpages table for a vpage with pagekey = errors.410
* Look on disk for a view called resources/views/errors/410.blade.php
* Look on disk for a view called resources/views/errors/410.twig
* Look in the vpages table for a vpage with pagekey = errors.404
* Look on disk for a view called resources/views/errors/404.blade.php
* Look on disk for a view called resources/views/errors/404.twig

For the details on how this works, see later under "Architecture"

## CMS Usage

There is an included controller class called VpageController (which you are welcome to
extend) that can be used as a catch-all route.  This controller contains one function
called `index()` which simply loads a page from the database using the URL that was
provided.  This gives you a simple blade based CMS for your application.

To include a route to this controller, include this route specification **after** all of
the other routes in your routes file(s):

```php
Route::any('{slug}', [
    'as'    => 'vpage.make',
    'uses'  => '\Delatbabel\ViewPages\Http\Controllers\VpageController@make'
])->where('slug', '.*');
```

You may need to add additional where clauses on this route to exclude other routes that
your application consumes.  e.g. to exclude all URLs under "/admin" and "/img" from this catch-all
route, add the following where clause:

```php
  ->where('slug', '^(?!admin)(?!img)([A-z\d-\/_.]+)?');
```

# TODO

* Extract the logic to find a page for a specific website from the make function and put
  it into a customised BelongsToMany class.
* More testing.  This *appears* to work in a sample application that I have imported it into
  but I haven't done extensive testing or built phpunit test cases yet.
* More documentation.
* Maybe a set of admin controllers to update / edit content in the database.  Use an in-browser
  editor like [HTMLiveCode](https://github.com/matthias-schuetz/HTMLiveCode).
* Fix the TwigEngine's finder so that it doesn't find Blade views, and vice-versa.  There is
  also a related TODO in `Vpage::make()` to restrict the view type pulled from the database.
* Add a lastModified() function to the loaders.
* Fix the isExpired() function in BladeCompiler.
* Maybe support other template engines such as Smarty.  In particular I would prefer a template
  engine that does not compile to PHP code and instead compiles to an in-memory string.

## Callouts

The original package that this was derived from had the idea of callouts.  This meant that
views could include calls like this:

```php
    {{ __o('toolbox@functionname') }}
```

__o was a helper function that called the Controller::call() function in Laravel 3 to render
the output of the "functionname" action on the toolbox controller in a HMVC like manner.  HMVC
is really no longer supported by Laravel 5 (and Taylor thinks that HMVC is a bad idea, which
I have to disagree with) so we need some other way of pulling in dynamic content.  This will
probably be via Repository or Service classes somehow.

[Service Injection](https://laravel.com/docs/5.1/blade#service-injection) may already work, I
haven't tested it.

# Architecture

I worked with a CMS system based on Laravel 3 that was fairly poor in its implementation,
this package is designed to be a best practice implementation of what the Laravel 3 CMS
was supposed to be.

## Factory

The root of the View system is the `Factory` class.  This is accessed by the `View` facade,
and a `view` singleton which is registered in the application.

The only addition that I made to the Laravel's default `Factory` class was to automatically
load the errors.410 view or then the errors.404 view if the requested view is not found.  This
is mostly of use in CMS applications where views may be searched for by URL path, and a user
may enter a garbage URL so we want to put up a 404 page and not just an unhandled exception block.

## Extensions and Engine Resolving

The `Factory` class contains an internal array called `$extensions` which is a mapping from a
file extension (specifically a view path extension) to an engine ID.  By default
the array looks like this:

```php
    protected $extensions = ['blade.php' => 'blade', 'php' => 'php'];
```

So the "blade.php" extension maps to the "blade" engine ID.

Additional engine extensions can be added to this `$extensions` array by calling `Factory::addExtension()`.
Note that this is already done by the TwigBridge service provider

The engine ID is passed to a resolver (`EngineResolver`) which contains a mapping from the engine
ID to the engine itself.  These mappings are created in the `EngineResolver::resolve()` function.

So each time we return a blade template path from the database it needs to have a "blade.php"
extension added to the template name so that the engine resolver can find the correct engine to
compile and load the blade.

## Engines

The `Engine` class wraps up the functionality of loading and then compiling and then evaluating
the compiled template (with the data).  This all happens in `get()`, and for blade templates more
specifically (or any template that gets compiled to PHP) there is a function called `evaluatePath()`
inside the `PhpEngine` class that includes the template directly and gets its output by wrapping
the include statement inside an `ob_start()` and `ob_end_clean()` pair (not efficient, which is
one of my reasons for disliking views that compile to PHP).

Evaluating a Twig template needs to be done differently to evaluating a Blade template because the
compiled result is not directly executable.  Note that this is already done in the Engine in `TwigBridge`.

## Loading Blade Views During Compilation

The Laravel View system is somewhat backwards.  The compilers each call the ViewFinders to find the
files and then load the files themselves (in the compiler) rather than the file finding, loading and
compiling (from string) happening independently.  So this required somewhat of a re-implementation
of the entire View system so that the loading stage and the compilation stage can happen separately.

### Finding

This was done by:

* Adding a `VpageViewFinder` class which can determine whether a view is found in the database.
* Adding a `ChainViewFinder` class which chained together the VpageViewFinder and the existing
  Laravel FileViewFinder class (left untouched) to first pick the view from the database and
  then fall back to the file system if it was not found there.

`EngineResolver` needs to have the extension added to the view name in order to be able to determine
the correct engine to hand the view to, so the `VpageViewFinder` adds the file extension to the view name.
The `Vpage` class which loads pages from the database can strip off this file extension before loading
the page in the `make()` function.

Note that the extension doesn't have to be prefixed with ".", it can have any prefix.  The `Vpage`
class defines the prefix to use as a constant.

### Loading

Extending the compiler to include a loading stage was done by:

* Creating a new `LoaderInterface` which defined the interface to a loader.  The only function
  that a loader needs is a get() function, to take the view name found by the finder and
  load it as a string.
* Creating new `FilesystemLoader` and `VpageLoader` classes which can take the view name and load
  it from the file system or database respectively.
* Creating a `ChainLoader` class which can chain together the `VpageLoader` and the `FilesystemLoader`
  objects to load the view from wherever it happens to be found.

The final step was to extend the native Laravel `BladeCompiler` class to load the view contents
using one of the loaders (initialised as the `ChainLoader`) instead of using its own internal
`Filesystem` object to load the view contents.  Compilation then happens as normal using the
`compileString()` function.

## Loading Twig Views

### Finding

The `ChainViewFinder` class is also able to be used by the `Twig` loader created in the `TwigBridge`
service provider, so that the twig loader's `ViewFinder` can use the same approach to finding
views that the Blade loader uses.

### Loading

`Twig` (via `TwigBridge`) already has the concept of a separate finder and loader class, however the
loader has to follow Twig's `Twig_LoaderInterface`.  To achieve this I built a `VpageTwigLoader` class
to conform to the interface.

I over-rode the `TwigBridge` `ServiceProvider` class to provide a `VpageTwigLoader` object in the
`Twig_Loader_Chain` object that is already used to load twig data.

### Other Implementations

In `modules/backend/twig`, [OctoberCMS](https://github.com/octobercms/october) has a bunch of extensions
to the twig classes to do loading, etc. They have an implementaton of `Twig_LoaderInterface` which
is still essentially a file based loader although it does a few extra Laravely things such as firing
events on load, and using its own CMS classes to do things like loading files (from disk or from
cache if they are present) using the Laravel `File` and `Cache` facades.  This was not what I wanted
to do at all.

## Handling View Names

A view can be found by name or URL.  A CMS may prefer to fetch views by URL, a system that
is just working on view names may prefer to fetch by page key (e.g. `layouts.master`).  The
Factory class attempts to find by key first, and then by URL.

If a view is not found in the database then a view by that key is searched for on disk.

If no view is found on the disk then the `errors.410` and then the `errors.404` views are searched
for in the database.

If no view is found at that point then an exception is thrown.

## Blade Compilation

Compilation of blade templates is a bit of a black art, that's poorly explained in the Laravel
documentation.  Basically, blade templates are all compiled to on-disk PHP files which are
then stored in `storage/framework/views`.  Once a compiled version of a template goes out of date
it is replaced with a newer copy.  The caching of these compiled templates normally depends on
the file date of the blade template file, however in this extension we make it depend on the
`updated_at` date of the template data in the database.

When a blade is compiled to PHP, the directives are compiled as follows:

### @extends / @section

These go together.  @extends compiles to:

```php
    echo $__env->make('layout.name', array_except(get_defined_vars(), array('__data', '__path')))->render();
```

@section and @endsecton compile to:

```php
    $__env->startSection('section_name');
    $__env->stopSection();
```

Note that the directives appear in the compiled file in the opposite order to which they appear
in the blade template file -- normally in the template file @extends would be at the top and
@section / @endsection would be below, in the compiled template file the compiled version of
@extends is at the end of the file.

### @yield

@yield('section_name') appears like this in the compiled file:

```php
    echo $__env->yieldContent('body');
```

### Use of $__env

The global variable `$__env` is actually an instance of `Illuminate\View\Factory`.

That class implements the necessary `make()`, `startSection()`, `stopSection()` and `yieldContent()`
functions, which make the content appear in the correct place.

The critical function is `make()` which does this:

* Normalize the view name by replacing '.' with '/'
* Calls the finder to find the view.
* Prepares the data array.
* Discovers the engine to be used from the path.
* Creates the `View` object

The problem with the base `Factory` class is that it assumes that the view name is a file
name that has an extension, and the "php" or "blade.php" or "twig" extensions can be
identified to determine the view type.  Instead I store the view type in the database
table.

The finder and loader have been extended so that they are able to pull the
view from the database instead of from disk.  The logic of pulling the view from the
database is all in the `Vpage::make()` function.

Once the content of the blade is pulled from the database and the engine is resolved
then the view factory and the engine are then passed by `Factory::make()` to the view.

### Rendering

`View::render()` does the actual rendering by:

* calling `View::renderContents()`
* which calls `View::getContents()`
* which passes the full view path and the data to the `Engine::get()`
* `Engine::get()` calls `Compiler::compile()` to compile the view if the cached copy of the compiled view is expired
* then `Compiler::getCompiledPath()` which returns the path of the compiled view
* then `Engine::evaluatePath()` which passes the data to the compiled view

## Service Provider

The service provider here is fairly simple -- however there are 3:

* `ViewPagesServiceProvider` -- does the normal registration of migrations, seeds, and also calls
  in `StringBladeCompilerServiceProvider`.
* `IlluminateViewServiceProvider` -- extends the base Laravel View Service Provider to include the
  necessary additional finder and loader classes.
* `TwigBridgeServiceProvider` -- extends the Service Provider in `TwigBridge` so that the twig chain loader includes
  a class to load twig templates from the database.

## Model Class

The model class (`Vpage`) replaces the on-disk storage of template files, so that the `Factory`
class discussed above can pull templates from the database rather than disk.

# References

* https://github.com/Flynsarmy/laravel-db-blade-compiler
* https://github.com/TerrePorter/StringBladeCompiler (the 3.0 branch)
* http://twig.sensiolabs.org/
* https://github.com/rcrowe/TwigBridge
* https://github.com/twigphp/Twig
* http://twig.sensiolabs.org/doc/recipes.html#using-a-database-to-store-templates
