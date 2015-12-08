<?php
/**
 * Convenience functionality for composer.lock files in WordPress projects.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a composer.lock file with convenience methods useful for
 * WordPress projects.
 */
class WordPressLock extends ComposerLock {
	/**
	 * Get an array of all WordPress core packages.
	 *
	 * @return array or null
	 */
	public function core_packages() {
		return $this->packages_by_type( 'wordpress-core' );
	}

	/**
	 * Get an array of all WordPress mu-plugin packages.
	 *
	 * @return array or null
	 */
	public function mu_plugin_packages() {
		return $this->packages_by_type( 'wordpress-muplugin' );
	}

	/**
	 * Get an array of all WordPress plugin packages.
	 *
	 * @return array or null
	 */
	public function plugin_packages() {
		return $this->packages_by_type( 'wordpress-plugin' );
	}

	/**
	 * Get an array of all WordPress theme packages.
	 *
	 * @return array or null
	 */
	public function theme_packages() {
		return $this->packages_by_type( 'wordpress-theme' );
	}

	/**
	 * Get an array of all WordPress packages.
	 *
	 * @return array or null
	 */
	public function wordpress_packages() {
		$core = $this->core_packages() ?: [];
		$mu = $this->mu_plugin_packages() ?: [];
		$plugin = $this->plugin_packages() ?: [];
		$theme = $this->theme_packages() ?: [];

		return array_merge( $core, $mu, $plugin, $theme );
	}

	/**
	 * Create a new WordPressPackage object from a given composer package object.
	 *
	 * @param stdClass $package composer.lock package.
	 *
	 * @return SSNepenthe\ComposerUtilities\LockPackage
	 */
	protected function instantiate_package( $package ) {
		return new WordPressPackage( $package );
	}
}
