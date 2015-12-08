<?php
/**
 * Convenience functionality for composer.json files in WordPress projects.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a composer.json file with convenience methods useful for
 * WordPress projects.
 */
class WordPressJson extends ComposerJson {
	/**
	 * Get the WordPress mu-plugins path.
	 *
	 * @return string
	 */
	public function mu_plugin_path() {
		return $this->path_by_name( 'wordpress-muplugin' );
	}

	/**
	 * Get the WordPress plugins path.
	 *
	 * @return string or null
	 */
	public function plugin_path() {
		return $this->path_by_name( 'wordpress-plugin' );
	}

	/**
	 * Get the WordPress themes path.
	 *
	 * @return string or null
	 */
	public function theme_path() {
		return $this->path_by_name( 'wordpress-theme' );
	}

	/**
	 * Get the WordPress install path.
	 *
	 * @return string or null
	 */
	public function wordpress_path() {
		return $this->path_by_name( 'wordpress-install-dir' );
	}

	/**
	 * Set all paths defined by the composer.json file.
	 */
	protected function set_paths() {
		parent::set_paths();

		$extra = isset( $this->object()->extra ) ?
			$this->object()->extra :
			false;
		$installer_paths = $extra && isset( $extra->{'installer-paths'} ) ?
			$extra->{'installer-paths'} :
			[];

		$this->paths['wordpress-install-dir'] = $extra && isset( $extra->{'wordpress-install-dir'} ) ? $extra->{'wordpress-install-dir'} : 'wordpress';

		foreach ( $installer_paths as $path => $types ) {
			$types = array_map( [ $this, 'strip_type' ], $types );

			foreach ( $types as $type ) {
				$this->paths[ $type ] = $path;
			}
		}

		if ( ! isset( $this->paths['wordpress-muplugin'] ) ) {
			$this->paths['wordpress-muplugin'] = 'wp-content/mu-plugins/{$name}/';
		}

		if ( ! isset( $this->paths['wordpress-plugin'] ) ) {
			$this->paths['wordpress-plugin'] = 'wp-content/plugins/{$name}/';
		}

		if ( ! isset( $this->paths['wordpress-theme'] ) ) {
			$this->paths['wordpress-theme'] = 'wp-content/themes/{$name}/';
		}
	}

	/**
	 * Strip the 'type:' substr from a package type.
	 *
	 * @param string $value Composer package type.
	 *
	 * @return string
	 */
	protected function strip_type( $value ) {
		if ( 'type:' === substr( $value, 0, 5 ) ) {
			$value = str_replace( 'type:', '', $value );
		}

		return $value;
	}
}
