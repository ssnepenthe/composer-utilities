# composer-utilities
Some basic utilities for working with Composer projects.

## Usage
This package consists of two classes:

### SSNepenthe\\ComposerUtilities\\ComposerJSON
```php
use SSNepenthe\ComposerUtilities\ComposerJSON;

$json = new ComposerJSON(__DIR__ . '/composer.json');

var_dump($json->hash()); // md5 hash of composer.json
var_dump($json->paths()); // array of installer paths
var_dump($json->filterPathsByName('vendor-dir')); // single installer path

```

Output will look to similar to the following:

```
string(32) "8f33f345f3cd4b1eae2b8f3ad36a426d"
array(5) {
  ["vendor-dir"]=>
  string(6) "vendor"
  ["wordpress-install-dir"]=>
  string(9) "wordpress"
  ["wordpress-muplugin"]=>
  string(30) "wp-content/mu-plugins/{$name}/"
  ["wordpress-plugin"]=>
  string(27) "wp-content/plugins/{$name}/"
  ["wordpress-theme"]=>
  string(26) "wp-content/themes/{$name}/"
}
string(6) "vendor"
```

There are also some convenience methods for specific paths:

```php
var_dump($json->muPluginPath());
var_dump($json->pluginPath());
var_dump($json->themePath());
var_dump($json->wordPressPath());
var_dump($json->vendorPath());
```

Looks like this:

```
string(30) "wp-content/mu-plugins/{$name}/"
string(27) "wp-content/plugins/{$name}/"
string(26) "wp-content/themes/{$name}/"
string(9) "wordpress"
string(6) "vendor"
```

Note that only `vendor-dir` has a default path - the others will return `NULL` if not explicitly set.

### SSNepenthe\\ComposerUtilities\\ComposerLock

```php
use SSNepenthe\ComposerUtilities\ComposerLock;

$lock = new ComposerJSON(__DIR__ . '/composer.lock');

var_dump($lock->hash()); // md5 hash of corresponding composer.json
var_dump($lock->packages()); // an array of dependencies, or empty array if none are set
var_dump($lock->devPackages()); // an array of dev-dependencies, or an empty array if none are set
var_dump($lock->filterPackagesByName('symfony/debug')); // an individual dependency object with name of 'symfony/debug' or NULL if it does not exist. Pass true as second param to search in dev-dependencies
var_dump($lock->filterPackagesByType('library')); // an array of package objects with type 'library' or NULL if none exist. Pass true as second param to search in dev-dependencies
```

Output will look something like this:

```
string(32) "12d44241aa342ff4acfa5f68a188edbf"
array(5) {
  [0]=>
  object(stdClass)#4 (17) {
    ["name"]=>
    string(19) "composer/installers"
    ["version"]=>
    string(7) "v1.0.22"

		[OMITTED]

  }
  [1]=>
  object(stdClass)#15 (12) {
    ["name"]=>
    string(7) "psr/log"
    ["version"]=>
    string(5) "1.0.0"

		[OMITTED]

  }
  [2]=>
  object(stdClass)#21 (16) {
    ["name"]=>
    string(13) "symfony/debug"
    ["version"]=>
    string(6) "v3.0.0"

		[OMITTED]

  }
  [3]=>
  object(stdClass)#33 (7) {
    ["name"]=>
    string(29) "wpackagist-plugin/cache-buddy"
    ["version"]=>
    string(5) "0.2.0"

		[OMITTED]

  }
  [4]=>
  object(stdClass)#37 (7) {
    ["name"]=>
    string(30) "wpackagist-theme/twentyfifteen"
    ["version"]=>
    string(3) "1.3"

		[OMITTED]

  }
}
array(0) {
}
object(stdClass)#21 (16) {
  ["name"]=>
  string(13) "symfony/debug"
  ["version"]=>
  string(6) "v3.0.0"

	[OMITTED]

}
array(2) {
  [0]=>
  object(stdClass)#15 (12) {
    ["name"]=>
    string(7) "psr/log"
    ["version"]=>
    string(5) "1.0.0"

		[OMITTED]

  }
  [1]=>
  object(stdClass)#21 (16) {
    ["name"]=>
    string(13) "symfony/debug"
    ["version"]=>
    string(6) "v3.0.0"

		[OMITTED]

  }
}
```
