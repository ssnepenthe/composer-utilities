# composer-utilities
Some basic utilities for for retrieving information about Composer projects from `composer.json` and `composer.lock`.

The WordPress implementations assume that you are using the `composer/installers` composer plugin, the `johnpbloch/wordpress` package for core and `wpackagist.org` as a composer repository.

## Usage
Instantiate with paths to your `composer.json` and `composer.lock` files.

Generic json:

```php
use SSNepenthe\ComposerUtilities\Json;

$json = new Json( __DIR__ . '/composer.json' );
```

Composer Specific:

```php
use SSNepenthe\ComposerUtilities\Composer\Json;
use SSNepenthe\ComposerUtilities\Composer\Lock;

$json = new Json( __DIR__ . '/composer.json' );
$lock = new Lock( __DIR__ . '/composer.lock' );
```

WordPress Specific:

```php
use SSNepenthe\ComposerUtilities\WordPress\Json;
use SSNepenthe\ComposerUtilities\WordPress\Lock;

$json = new Json( __DIR__ . '/composer.json' );
$lock = new Lock( __DIR__ . '/composer.lock' );
```

`SSNepenthe\ComposerUtilities\Json` is the base class that provides some convenience methods for working with json files:

```php
$json->json(); // a string containing the full contents of the passed json file, throws RuntimeException if the file does not exist
$json->object(); // same as json_decode( $json->json() ) but throws a RuntimeException if there are any errors decoding the file
$json->path(); // the path passed to the contructor
```

`SSNepenthe\ComposerUtilities\Composer\Json` extends `SSNepenthe\ComposerUtilities\Json`, adding the following methods:

```php
$json->hash(); // md5 hash of the file
$json->path_by_name( 'type:wordpress-plugin' ); // the install path as set in composer.json extra->{'installer-paths'}, null if not set
$json->paths(); // an array of all paths from extra->{'installer-paths'}
$json->vendor_path(); // alias of $json->path_by_name( 'vendor-dir' )
```

`SSNepenthe\ComposerUtilities\WordPress\Json` extends `SSNepenthe\ComposerUtilities\Composer\Json`, adding the following methods:

```php
$json->mu_plugin_path(); // alias of $json->path_by_name( 'type:wordpress-muplugin' )
$json->plugin_path(); // alias of $json->path_by_name( 'type:wordpress-plugin' )
$json->theme_path(); // alias of $json->path_by_name( 'type:wordpress-theme' )
$json->wordpress_path(); // alias of $json->path_by_name( 'wordpress-install-dir' )
```

`SSNepenthe\ComposerUtilities\Composer\Lock` extends `SSNepenthe\ComposerUtilities\Json`, adding the following methods:

```php
$lock->dev_packages(); // alias of $lock->packages( true )
$lock->hash(); // md5 hash of the composer.lock file
$lock->json_hash(); // md5 hash of the composer.json file from which this lock file was generated
$lock->name_index(); // an index of packages useful for searching by name
$lock->package_by_name( 'johnpbloch/wordpress' ); // get the package named 'johnpbloch/wordpress'
$lock->packages(); // an array of composer dependencies, pass true as first parameter to get dev-dependencies instead
$lock->packages_by_type( 'wordpress-plugin' ); // an array of all packages with type of wordpress-plugin
$lock->type_index(); // an index of packages useful for searching by type
```

Note that individual packages are returned as an instance of `SSNepenthe\ComposerUtilities\Composer\Package` with the following methods:

```php
$package->is_composer_plugin(); // alias of $package->is_of_type( 'composer-plugin' )
$package->is_library(); // alias of $package->is_of_type( 'library' )
$package->is_of_type( 'wordpress-plugin' ); // bool
$package->name(); // string
$package->type(); // string
$package->version(); // string
```

`SSNepenthe\ComposerUtilities\WordPress\Lock` extends `SSNepenthe\ComposerUtilities\Composer\Lock`, adding the following methods:

```php
$lock->core_packages(); // alias of $lock->packages_by_type( 'wordpress-core' )
$lock->mu_plugin_packages(); // alias of $lock->packages_by_type( 'wordpress-muplugin' )
$lock->plugin_packages(); // alias of $lock->packages_by_type( 'wordpress-plugin' )
$lock->theme_packages(); // alias of $lock->packages_by_type( 'wordpress-theme' )
$lock->wordpress_packages(); // an array of all WordPress packages
```

Note that individual packages will be returned as an instance of `SSNepenthe\ComposerUtilities\WordPress\Package`, which extends `SSNepenthe\ComposerUtilities\Composer\Package` with the following methods:

```php
$package->is_wp_core(); // alias of $package->is_of_type( 'wordpress-core' )
$package->is_wp_mu_plugin(); // alias of $package->is_of_type( 'wordpress-muplugin' )
$package->is_wp_package(); // bool
$package->is_wp_plugin(); // alias of $package->is_of_type( 'wordpress-plugin' )
$package->is_wp_theme(); // alias of $package->is_of_type( 'wordpress-theme' )
$package->is_wpackagist_package(); // bool
```
