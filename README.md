# viewpages

Support view/rendering of Laravel pages and templates from a database.

Can be used for content management, admin interfaces (e.g. using AdminLTE or other
front end frameworks), etc.

**UNDER CONSTRUCTION, NOT YET READY FOR USE**

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

After composer update completes, add these lines to your config/app.php file in the 'providers' array:

```
    Delatbabel\SiteConfig\SiteConfigServiceProvider::class,
    Delatbabel\ViewPages\ViewPagesServiceProvider::class,
```

In the same file, replace this line (or comment it out):

```
    Illuminate\View\ViewServiceProvider::class
```

with this:

```
    Wpb\String_Blade_Compiler\ViewServiceProvider::class,
```

### Incorporate and Run the Migrations

Finally, incorporate and run the migration scripts to create the database tables as follows:

```php
    php artisan vendor:publish --tag=migrations --force
    php artisan vendor:publish --tag=seeds --force
    php artisan migrate
```

# TODO

* Code to pull and render a page from the database.
* Extract the logic to find a page for a specific website from the make function and put
  it into a customised BelongsToMany class.
* Be able to handle all of the various directives in a normal Blade template
  such as @extends, @section / @endsection, etc.  See **Handling Directives** below.
* More testing.
* More documentation.
* Sample templates based on AdminLTE
* Maybe a set of admin controlle

## Handling Directives

It would be useful to be able to handle all of the directives in a normal Blade
template in some way.

@extends should pull in the template from the Vptemplate model class.

@include and @extends could be problematic as they have to decide whether to use a template
or page.  @extends would normally refer to a template, @include in a template would refer
to a template, in a page would refer to another page.  Perhaps we need to amalgamate the two
classes (pages and templates).

Next issue is that inside the existing Laravel view classes, directives such as @include are
complied to PHP code that in turn calls functions inside the view engine.  The extensions to
these classes in String_Blade_Compiler don't sufficiently change these functions so that
@include can refer to another template inside a model class -- it handles either an array
(including string data) or a view name which is assumed to be a view on disk.

So we may need another extension of the String_Blade_Compiler class to be able to have
@include refer to a page or template name instead of a file name.

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

These are website dependent data blocks.  They could just as easily be implemented as templates
or pages.  It may also be preferable to store website dependent configuration data including
renderable data blocks in the configs table, which is of course what it was designed for.

# Architecture

TODO
