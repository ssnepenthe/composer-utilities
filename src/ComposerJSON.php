<?php
/**
 * Convenience functionality for composer.json files.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a composer.json file.
 */
class ComposerJSON
{
	/**
	 * The hash of our composer.json file.
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * JSON decoded compoer.json file.
	 *
	 * @var array
	 */
	protected $json;

	/**
	 * All of our package install paths.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * Set up our object.
	 *
	 * @param string $json Path to composer.json file.
	 *
	 * @throws \InvalidArgumentException If passed argument is not a string.
	 * @throws \RuntimeException If the passed argument does not point to a valid file.
	 */
	public function __construct( $json = 'composer.json' ) {
		if ( ! is_string( $json ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $json )
				)
			);
		}

		if ( is_dir( $json ) ) {
			$json .= '/composer.json';
		}

		if ( ! is_file( $json ) ) {
			throw new \RuntimeException(
				'Supplied json file does not exist.'
			);
		}

		$file = file_get_contents( $json );

		$this->hash = md5( $file );
		$this->json = json_decode( $file );
	}

	/**
	 * Get the hash of this composer.json file.
	 *
	 * @return string
	 */
	public function hash() {
		return $this->hash;
	}

	/**
	 * Get the WordPress mu-plugins path.
	 *
	 * @return string
	 */
	public function mu_plugin_path() {
		return $this->filter_paths_by_name( 'wordpress-muplugin' );
	}

	/**
	 * Get a single install path by name.
	 *
	 * @param string $name Install path name.
	 *
	 * @throws \InvalidArgumentException If the passed argument is not a string.
	 *
	 * @return string or null
	 */
	public function path_by_name( $name ) {
		if ( ! is_string( $name ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $name )
				)
			);
		}

		return isset( $this->paths()[ $name ] ) ? $this->paths()[ $name ] : null;
	}

	/**
	 * Get all install paths.
	 *
	 * @return array
	 */
	public function paths() {
		if ( ! isset( $this->paths ) ) {
			$this->set_paths();
		}

		return $this->paths;
	}

	/**
	 * Get the WordPress plugins path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return $this->filter_paths_by_name( 'wordpress-plugin' );
	}

	/**
	 * Get the WordPress themes path.
	 *
	 * @return string
	 */
	public function theme_path() {
		return $this->filter_paths_by_name( 'wordpress-theme' );
	}

	/**
	 * Get the composer vendor path.
	 *
	 * @return string
	 */
	public function vendor_path() {
		return $this->filter_paths_by_name( 'vendor-dir' );
	}

	/**
	 * Get the WordPress install path.
	 *
	 * @return string
	 */
	public function wordpress_path() {
		return $this->filter_paths_by_name( 'wordpress-install-dir' );
	}

	/**
	 * Find and save all paths set in this composer.json file.
	 *
	 * @return void
	 */
	protected function set_paths() {
		$config = isset( $this->json->config ) ?
			$this->json->config :
			false;
		$extra = isset( $this->json->extra ) ?
			$this->json->extra :
			false;
		$installer_paths = $extra && isset( $extra->{'installer-paths'} ) ?
			$extra->{'installer-paths'} :
			[];

		$this->paths['vendor-dir'] = $config && isset( $config->{'vendor-dir'} ) ?
			$config->{'vendor-dir'} :
			'vendor';

		if ( $extra && isset( $extra->{'wordpress-install-dir'} ) ) {
			$this->paths['wordpress-install-dir'] = $extra->{'wordpress-install-dir'};
		}

		foreach ( $installer_paths as $path => $types ) {
			$types = array_map( [ $this, 'strip_type' ], $types );

			foreach ( $types as $type ) {
				$this->paths[ $type ] = $path;
			}
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
