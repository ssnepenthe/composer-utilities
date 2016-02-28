<?php
/**
 * Convenience functionality for composer.json files in WordPress projects.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities\WordPress;

use SSNepenthe\ComposerUtilities\Composer\Json as ComposerJson;

/**
 * This class wraps a composer.json file with convenience methods useful for
 * WordPress projects.
 */
class Json extends ComposerJson {
	/**
	 * Get the WordPress mu-plugins path.
	 *
	 * @return string
	 */
	public function mu_plugin_path() {
		return $this->path_by_name( 'type:wordpress-muplugin' );
	}

	/**
	 * Get the WordPress plugins path.
	 *
	 * @return string or null
	 */
	public function plugin_path() {
		return $this->path_by_name( 'type:wordpress-plugin' );
	}

	/**
	 * Get the WordPress themes path.
	 *
	 * @return string or null
	 */
	public function theme_path() {
		return $this->path_by_name( 'type:wordpress-theme' );
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
		$this->paths['wordpress-install-dir'] = $extra && isset( $extra->{'wordpress-install-dir'} ) ? $extra->{'wordpress-install-dir'} : 'wordpress';

		if ( ! isset( $this->paths['type:wordpress-muplugin'] ) ) {
			$this->paths['type:wordpress-muplugin'] = 'wp-content/mu-plugins/{$name}/';
		}

		if ( ! isset( $this->paths['type:wordpress-plugin'] ) ) {
			$this->paths['type:wordpress-plugin'] = 'wp-content/plugins/{$name}/';
		}

		if ( ! isset( $this->paths['type:wordpress-theme'] ) ) {
			$this->paths['type:wordpress-theme'] = 'wp-content/themes/{$name}/';
		}
	}
}
