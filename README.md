# composer-utilities
Some basic utilities for working with Composer projects.

The WordPress implementations assume that you are using the `composer/installers` composer plugin, the `johnpbloch/wordpress` package for core and `wpackagist.org` as a composer repository.

## Usage
Instantiate with paths to your `composer.json` and `composer.lock` files.

Generic json:

```php
use SSNepenthe\ComposerUtilities\JsonFile;

$json = new JsonFile( __DIR__ . '/composer.json' );
```

Composer Specific:

```php
use SSNepenthe\ComposerUtilities\ComposerJson;
use SSNepenthe\ComposerUtilities\ComposerLock;

$json = new ComposerJson( __DIR__ . '/composer.json' );
$lock = new ComposerLock( __DIR__ . '/composer.lock' );
```

WordPress Specific:

```php
use SSNepenthe\ComposerUtilities\WordPressJson;
use SSNepenthe\ComposerUtilities\WordPressLock;

$json = new ComposerLock( __DIR__ . '/composer.lock' );
$lock = new ComposerLock( __DIR__ . '/composer.lock' );
```

`JsonFile` is the base class that provides some convenience methods for working with json files:

```php
$json->json(); // a string containing the full contents of the passed json file, throws RuntimeException if the file does not exist
$json->object(); // same as json_decode( $json->json() ) but throws a RuntimeException if there are any errors decoding the file
$json->path(); // the path passed to the contructor
```

`ComposerJson` extends `JsonFile`, adding the following methods:

```php
$json->hash(); // md5 hash of the file
$json->path_by_name( 'wordpress-plugin' ); // the path to the named package type as set in your composer.json file, null if not set
$json->paths(); // an array of all paths set in composer.json
$json->vendor_path(); // path corresponding to config->{'vendor-dir'}
```

`WordPressJson` extends `ComposerJson`, adding the following methods:

```php
$json->mu_plugin_path(); // path corresponding to wordpress-muplugin in extra->{'installer-paths'}
$json->plugin_path(); // path corresponding to wordpress-plugin in extra->{'installer-paths'}
$json->theme_path(); // path corresponding to wordpress-theme in extra->{'installer-paths'}
$json->wordpress_path(); // path corresponding to extra->{'wordpress-install-dir'}
```

`ComposerLock` extends `JsonFile`, adding the following methods:

```php
$lock->dev_packages(); // an array of dev-dependencies
$lock->hash(); // md5 hash of the composer.lock file
$lock->json_hash(); // md5 hash of the composer.json file from which this lock file was generated
$lock->name_index(); // an index of packages useful for searching by name
$lock->package_by_name( 'johnpbloch/wordpress' ); // get the package named 'johnpbloch/wordpress'
$lock->packages(); // an array of dependencies
$lock->packages_by_type( 'wordpress-plugin' ); // an array of all wordpress-plugin packages
$lock->type_index(); // an index of packages useful for searching by type
```

Note that individual packages are returned as an instance of `SSNepenthe\ComposerUtilities\LockPackage` with the following methods:

```php
$package->is_composer_plugin(); // bool
$package->is_library(); // bool
$package->is_of_type( 'wordpress-plugin' ); // bool
$package->name(); // string
$package->type(); // string
$package->version(); // string
```

`WordPressLock` extends `ComposerLock`, adding the following methods:

```php
$lock->core_packages(); // an array of all packages of type 'wordpress-core'
$lock->mu_plugin_packages(); // an array of all packages of type 'wordpress-muplugin'
$lock->plugin_packages(); // an array of all packages of type 'wordpress-plugin'
$lock->theme_packages(); // an array of all packages of type 'wordpress-theme'
$lock->wordpress_packages(); // an array of all WordPress packages
```

Note that individual packages will be returned as an instance of `SSNepenthe\ComposerUtilities\WordPressPackage`, which extends `LockPackage` with the following methods:

```php
$package->is_wp_core(); // bool
$package->is_wp_mu_plugin(); // bool
$package->is_wp_package(); // bool
$package->is_wp_plugin(); // bool
$package->is_wp_theme(); // bool
$package->is_wpackagist_package(); // bool
```
