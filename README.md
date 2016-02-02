# viewpages

Support view/rendering of Laravel pages and templates from a database.

Can be used for content management, admin interfaces (e.g. using AdminLTE or other
front end frameworks), etc.

**UNDER CONSTRUCTION, NOT YET READY FOR USE**

## Rationale

The lack of ability to have database backed views, templates, and layouts is one of the
missing features that prevents Larvel from being used to create a truly dynamic CMS.  This
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
* Be able to handle all of the various directives in a normal Blade template
  such as @extends, @section / @endsection, etc.  See **Handling Directives** below.
* More testing.
* More documentation.
* Maybe a set of admin controllers to update / edit content in the database.

## Handling Directives

It would be useful to be able to handle all of the directives in a normal Blade
template in some way.

The issue is that inside the existing Laravel view classes, directives such as @include are
complied to PHP code that in turn calls functions inside the view engine.  The extensions to
these classes in String_Blade_Compiler don't sufficiently change these functions so that
@include can refer to another template inside a model class -- it handles either an array
(including string data) or a view name which is assumed to be a view on disk.

So we may need another extension of the String_Blade_Compiler class to be able to have
@include refer to a page or template key instead of a file name.  See **Blade Compilation**
below.

I had included a hack wereby you can include a {{ $page_content }} value
in a template to have the associated page content included for testing purposes.  I have
since removed this.

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

The critical function is make() which needs to be extended so that it is able to pull the
view from the database instead of from disk.

I *think* that's the only change that needs making in an extended version of Factory.

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

However it's fairly early in the design and proof of concept phase at the moment, and a lot
of work needs to be done to determine what those best practices are going to be.

# References

* https://github.com/Flynsarmy/laravel-db-blade-compiler
* https://github.com/TerrePorter/StringBladeCompiler (the 3.0 branch)
