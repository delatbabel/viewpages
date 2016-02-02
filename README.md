# viewpages

Support view/rendering of Laravel pages and templates from a database.

Can be used for content management, admin interfaces (e.g. using AdminLTE or other
front end frameworks), etc.

**UNDER CONSTRUCTION, NOT ENTIRELY STABLE YET**

## Rationale

The lack of ability to have database backed views, templates, and layouts is one of the
missing features that prevents Laravel from being used to create a truly dynamic CMS.  This
package aims to fix that.

Volunteers to help code this would be welcomed.

TerrePorter partially solves this issue with his StringBladeCompiler package, which is
used by this package as a dependency.  His package was originally based on Flynsarmy/laravel-db-blade-compiler
which did support taking a blade from a model object but is no longer maintained.

## Installation

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

### Register Service Provider

After composer update completes, remove this line from your config/app.php file in the 'providers'
array (or comment it out):

```
    Illuminate\View\ViewServiceProvider::class
```

Replace it with this line:

```
    Delatbabel\ViewPages\ViewPagesServiceProvider::class,
```

### Incorporate and Run the Migrations

Finally, incorporate and run the migration scripts to create the database tables as follows:

```php
    php artisan vendor:publish --tag=migrations --force
    php artisan vendor:publish --tag=seeds --force
    php artisan migrate
```

# TODO

* Extract the logic to find a page for a specific website from the make function and put
  it into a customised BelongsToMany class.
* More testing.
* More documentation.
* Maybe a set of admin controllers to update / edit content in the database.

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

## Website Data Objects and Blocks

These are website dependent data blocks.  It may be preferable to store website dependent
data including renderable data blocks in the configs table, which is of course what it was
designed for.

# Architecture

I worked with a CMS system based on Laravel 3 that was fairly poor in its implementation,
this package is designed to be a best practice implementation of what the Laravel 3 CMS
was supposed to be.

## Handling Directives

I have extended the Factory class within String_Blade_Compiler class to be able to have
@include refer to a page or template key instead of a file name.  See **Blade Compilation**
below.

## Handling View Names

A view can be found by name or URL.  A CMS may prefer to fetch views by URL, a system that
is just working on view names may prefer to fetch by page key (e.g. layouts.master).  The
Factory class attempts to find by key first, and then by URL.

If a view is not found in the database then a view by that key is searched for on disk.

If no view is found on the disk then the errors.410 and then the errors.404 views are searched
for in the database.

If no view is found at that point then an exception is thrown.

## Blade Compilation

Compilation of blade templates is a bit of a black art, that's poorly explained in the Laravel
documentation.  Basically, blade templates are all compiled to on-disk PHP files which are
then stored in storage/framework/views.  Once a compiled version of a template goes out of date
it is replaced with a newer copy.  The caching of these compiled templates normally depends on
the file date of the blade template file, however in this extension we make it depend on the
updated_at date of the template data in the database.

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

The global variable $__env is actually an instance of Illuminate\View\Factory, or in the
String_Blade_Compiler extension it is an instance of Wpb\String_Blade_Compiler\Factory.

That class implements the necessary make(), startSection(), stopSection() and yieldContent()
functions, which make the content appear in the correct place.

The critical function is make() which has been extended so that it is able to pull the
view from the database instead of from disk.  The logic of pulling the view from the
database is all in the Vpage::make() function.  Once the content of the blade is pulled
from the database then it's passed back up to String_Blade_Compiler\Factory::make to do
the actual rendering.

## Service Provider

The service provider here is fairly simple -- however there are 2:

* ViewPagesServiceProvider -- does the normal registration of migrations, seeds, and also calls
  in ViewServiceProvider.
* ViewServiceProvider -- extends the Service Provider in String_Blade_Compiler so that my own
  Factory class is inserted when the factory is registered rather than the original.

## Model Class

The model class (Vpage) replaces the on-disk storage of template files, so that the Factory
class discussed above can pull templates from the database rather than disk.

# References

* https://github.com/Flynsarmy/laravel-db-blade-compiler
* https://github.com/TerrePorter/StringBladeCompiler (the 3.0 branch)
