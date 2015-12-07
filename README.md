# composer-utilities
Some basic utilities for working with Composer projects.

More specifically, this is geared toward WordPress development with Composer under the assumption that you are using the `composer/installers` composer plugin, the `johnpbloch/wordpress` package for core and `wpackagist.org` as a composer repository.

## Usage
Instantiate with paths to your `composer.json` and `composer.lock` files.

```php
use SSNepenthe\ComposerUtilities\ComposerJSON;
use SSNepenthe\ComposerUtilities\ComposerLock;

$json = new ComposerJSON( __DIR__ . '/composer.json' );
$lock = new ComposerLock( __DIR__ . '/composer.lock' );
```

`ComposerJSON` provides the following methods:

```php
$json->hash(); // return a hash of the file
$json->path_by_name( 'wordpress-plugin' ); // return the path to the named package type as set in your composer.json file, null if not set
$json->paths(); // return an array of all paths set in composer.json
```

And some convenience methods:
```php
$json->vendor_path(); // path corresponding to `config->{'vendor-dir'}`
$json->mu_plugin_path(); // path corresponding to wordpress-muplugin in `extra->{'installer-paths'}`
$json->plugin_path(); // path corresponding to wordpress-plugin in `extra->{'installer-paths'}`
$json->theme_path(); // path corresponding to wordpress-theme in `extra->{'installer-paths'}`
$json->wordpress_path(); // path corresponding to `extra->{'wordpress-install-dir'}`
```

Note that only `vendor-dir` has a default path - the others will return `NULL` if not explicitly set.

`ComposerLock` provides the following methods:

```php
$lock->dev_packages(); // array of dev-dependencies
$lock->hash(); // hash of the composer.lock file
$lock->json_hash(); // hash of the composer.json file from which this lock file was generated.
$lock->package_by_name( 'johnpbloch/wordpress' ); // get the package named 'johnpbloch/wordpress'
$lock->packages(); // array of dependencies
$lock->packages_by_type( 'wordpress-plugin' ); // an array of all wordpress-plugin packages
```

Note that all packages are returned as an instance of `SSNepenthe\ComposerUtilities\LockPackage` with the following methods:

```php
$package->is_of_type( 'wordpress-plugin' ); // bool
$package->is_wp_core(); // bool
$package->is_wp_mu_plugin(); // bool
$package->is_wp_package(); // bool
$package->is_wp_plugin(); // bool
$package->is_wp_theme(); // bool
$package->is_wpackagist_package(); // bool
$package->name(); // string
$package->type() // string
$package->version(); // string
```
