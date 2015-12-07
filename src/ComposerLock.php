<?php
/**
 * Convenience functionality for composer.lock files.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a composer.lock file.
 */
class ComposerLock
{
	/**
	 * The hash of this composer.lock file
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * JSON decoded composer.lock file.
	 *
	 * @var array
	 */
	protected $lock;

	/**
	 * Index of packages by name.
	 *
	 * @var array
	 */
	protected $name_index;

	/**
	 * Index of packages by type.
	 *
	 * @var array
	 */
	protected $type_index;

	/**
	 * Set up our object.
	 *
	 * @param string $lock Path to composer.lock file.
	 *
	 * @throws \InvalidArgumentException If passed argument is not a string.
	 * @throws \RuntimeException If passed argument is not a valid file path.
	 */
	public function __construct( $lock = 'composer.lock' ) {
		if ( ! is_string( $lock ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $lock )
				)
			);
		}

		if ( is_dir( $lock ) ) {
			$lock .= '/composer.lock';
		}

		if ( ! is_file( $lock ) ) {
			throw new \RuntimeException(
				'Supplied lock file does not exist.'
			);
		}

		$file = file_get_contents( $lock );

		$this->hash = md5( $file );
		$this->lock = json_decode( $file, true );

		$this->lock['packages'] = array_map(
			[ $this, 'instantiate_package' ],
			$this->lock['packages']
		);

		$this->lock['packages-dev'] = array_map(
			[ $this, 'instantiate_package' ],
			$this->lock['packages-dev']
		);

		$this->generate_indices();
	}

	/**
	 * Get an array of dev-dependencies.
	 *
	 * @return array
	 */
	public function dev_packages() {
		return $this->packages_by_environment( 'development' );
	}

	/**
	 * Get the hash of this composer.lock file.
	 *
	 * @return string
	 */
	public function hash() {
		return $this->hash;
	}

	/**
	 * Get the stored composer.json hash from this composer.lock file.
	 *
	 * @return string
	 */
	public function json_hash() {
		return $this->lock['hash'];
	}

	/**
	 * Search for a single package by name.
	 *
	 * @param string $name A package name to search for.
	 *
	 * @throws \InvalidArgumentException If passed argument is not a string.
	 *
	 * @return SSNepenthe\ComposerUtilities\LockPackage
	 */
	public function package_by_name( $name ) {
		if ( ! is_string( $name ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $name )
				)
			);
		}

		if ( ! isset( $this->name_index[ $name ] ) ) {
			return null;
		}

		$dev = $this->name_index[ $name ]['dev'] ? 'packages-dev' : 'packages';
		$key = $this->name_index[ $name ]['key'];

		return $this->lock[ $dev ][ $key ];
	}

	/**
	 * Get an array of dependencies.
	 *
	 * @return array
	 */
	public function packages() {
		return $this->packages_by_environment( 'production' );
	}

	/**
	 * Get an array of packages based on dependency type.
	 *
	 * @param string $environment d, dev, or development for dev-dependencies.
	 *
	 * @throws \InvalidArgumentException If passed argument is not a string.
	 *
	 * @return array
	 */
	public function packages_by_environment( $environment ) {
		if ( ! is_string( $environment ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $environment )
				)
			);
		}

		switch ( $environment ) {
			case 'd':
			case 'dev':
			case 'development':
				$key = 'packages-dev';
				break;
			default:
				$key = 'packages';
				break;
		}

		return $this->lock[ $key ];
	}

	/**
	 * Get an array of packages based on package type.
	 *
	 * @param string $type Package type to search for.
	 *
	 * @throws \InvalidArgumentException If passed argument is not a string.
	 *
	 * @return array
	 */
	public function packages_by_type( $type ) {
		if ( ! is_string( $type ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 passed to %s must be of type string, %s given.',
					__METHOD__,
					gettype( $type )
				)
			);
		}

		if ( ! isset( $this->type_index[ $type ] ) ) {
			return null;
		}

		$r = [];

		foreach ( $this->type_index[ $type ] as $index ) {
			$dev = $index['dev'] ? 'packages-dev' : 'packages';
			$key = $index['key'];

			$r[] = $this->lock[ $dev ][ $key ];
		}

		return $r;
	}

	/**
	 * Generate package indices for easier package lookups.
	 *
	 * @return void
	 */
	protected function generate_indices() {
		foreach ( $this->lock['packages'] as $key => $package ) {
			$this->name_index[ $package->name() ] = [
				'dev' => false,
				'key' => $key,
			];

			$this->type_index[ $package->type() ][] = [
				'dev' => false,
				'key' => $key,
			];
		}

		foreach ( $this->lock['packages-dev'] as $key => $package ) {
			$this->name_index[ $package->name() ] = [
				'dev' => true,
				'key' => $key,
			];

			$this->type_index[ $package->type() ][] = [
				'dev' => true,
				'key' => $key,
			];
		}
	}

	/**
	 * Create a new LockPackage object from a given composer package array.
	 *
	 * @param array $package composer.lock package.
	 *
	 * @return SSNepenthe\ComposerUtilities\LockPackage
	 */
	protected function instantiate_package( $package ) {
		return new LockPackage( $package );
	}
}
