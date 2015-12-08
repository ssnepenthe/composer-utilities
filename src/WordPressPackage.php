<?php
/**
 * Convenience functionality for composer.lock packages in WordPress projects.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a single package from a composer.lock file with convenience
 * methods useful for WordPress projects.
 */
class WordPressPackage extends LockPackage {
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
		return 'wordpress-' === substr( $this->object->type, 0, 10 );
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
	 * Determine whether or not this package is a wpackagist.org package.
	 *
	 * @return boolean
	 */
	public function is_wpackagist_package() {
		return 'wpackagist-' === substr( $this->object->name, 0, 11 );
	}
}
