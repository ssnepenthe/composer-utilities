<?php
/**
 * Convenience functionality for composer.lock packages.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a single package from a JSON decoded composer.lock file.
 */
class LockPackage
{
	/**
	 * JSON decoded package from composer.lock.
	 *
	 * @var array
	 */
	protected $package;

	/**
	 * Set up our object.
	 *
	 * @param array $package JSON decoded package from composer.lock.
	 */
	public function __construct( $package ) {
		$this->package = $package;
	}

	/**
	 * Determine whether or not this package is of the specified type.
	 *
	 * @param string $type Package type to check for.
	 *
	 * @return boolean
	 */
	public function is_of_type( $type ) {
		return $type === $this->package['type'];
	}

	/**
	 * Determine whether or not this package is a wpackagist.org package.
	 *
	 * @return boolean
	 */
	public function is_wpackagist_package() {

		return 'wpackagist-' === substr( $this->package['name'], 0, 11 );
	}

	/**
	 * Determine whether or not this package is the WordPress core.
	 *
	 * @return boolean
	 */
	public function is_wp_core() {

		return $this->is_of_type( 'wordpress-core' );
	}

	/**
	 * Determine whether or not this package is a WordPress mu-plugin.
	 *
	 * @return boolean
	 */
	public function is_wp_mu_plugin() {

		return $this->is_of_type( 'wordpress-muplugin' );
	}

	/**
	 * Determine whether or not this package is a WordPress package.
	 *
	 * @return boolean
	 */
	public function is_wp_package() {

		return 'wordpress-' === substr( $this->package['type'], 0, 10 );
	}

	/**
	 * Determine whether or not this package is a WordPress plugin.
	 *
	 * @return boolean
	 */
	public function is_wp_plugin() {

		return $this->is_of_type( 'wordpress-plugin' );
	}

	/**
	 * Determine whether or not this package is a WordPress theme.
	 *
	 * @return boolean
	 */
	public function is_wp_theme() {

		return $this->is_of_type( 'wordpress-theme' );
	}

	/**
	 * Get package name.
	 *
	 * @return string
	 */
	public function name() {

		return $this->package['name'];
	}

	/**
	 * Get package type.
	 *
	 * @return string
	 */
	public function type() {

		return $this->package['type'];
	}

	/**
	 * Get package version.
	 *
	 * @return string
	 */
	public function version() {

		return $this->package['version'];
	}
}
