<?php
/**
 * Convenience functionality for composer.json files.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities\Composer;

use SSNepenthe\ComposerUtilities\Json as JsonFile;

/**
 * This class wraps a composer.json file.
 */
class Json extends JsonFile {
	/**
	 * MD5 hash of the file.
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * List of all configured install paths.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * Set up our object.
	 *
	 * @param string $path Path to composer.json file.
	 */
	public function __construct( $path = 'composer.json' ) {
		$path = realpath( $path );

		if ( is_dir( $path ) ) {
			$path .= '/composer.json';
		}

		parent::__construct( $path );
	}

	/**
	 * Get the hash of this composer.json file.
	 *
	 * @return string
	 */
	public function hash() {
		if ( ! isset( $this->hash ) ) {
			$this->hash = md5( $this->json() );
		}

		return $this->hash;
	}

	/**
	 * Get a single install path by name.
	 *
	 * @param string $name Install path name.
	 *
	 * @return string or null
	 */
	public function path_by_name( $name ) {
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
	 * Get the composer vendor path.
	 *
	 * @return string
	 */
	public function vendor_path() {
		return $this->path_by_name( 'vendor-dir' );
	}

	/**
	 * Find and save all paths set in this composer.json file.
	 */
	protected function set_paths() {
		$config = isset( $this->object()->config ) ?
			$this->object()->config :
			false;
		$extra = isset( $this->object()->extra ) ?
			$this->object()->extra :
			false;
		$installer_paths = $extra && isset( $extra->{'installer-paths'} ) ?
			$extra->{'installer-paths'} :
			[];
		$this->paths['vendor-dir'] = $config && isset( $config->{'vendor-dir'} ) ?
			$config->{'vendor-dir'} :
			'vendor';

		foreach ( $installer_paths as $path => $types ) {
			foreach ( $types as $type ) {
				$this->paths[ $type ] = $path;
			}
		}
	}
}
