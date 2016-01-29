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
    php artisan migrate
```

# TODO

* Code to pull and render a page from the database.
* More testing.
* More documentation.
* Sample templates based on AdminLTE
* Maybe a set of admin controllers/methods for updating the templates and pages.

# Architecture

TODO
