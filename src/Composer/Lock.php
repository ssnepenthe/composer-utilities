<?php
/**
 * Convenience functionality for composer.lock files.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities\Composer;

use SSNepenthe\ComposerUtilities\Json as JsonFile;

/**
 * This class wraps a composer.lock file.
 */
class Lock extends JsonFile {
	/**
	 * MD5 hash of this composer.lock file
	 *
	 * @var string
	 */
	protected $hash;

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
	 * @param string $path Path to composer.lock file.
	 */
	public function __construct( $path = 'composer.lock' ) {
		$path = realpath( $path );

		if ( is_dir( $path ) ) {
			$path .= '/composer.lock';
		}

		parent::__construct( $path );
	}

	/**
	 * Get an array of dev packages.
	 *
	 * @return array
	 */
	public function dev_packages() {
		return $this->packages( true );
	}

	/**
	 * Get the hash of this composer.lock file.
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
	 * Get the MD5 hash of the composer.json file used to generate this lock file.
	 *
	 * @return string
	 */
	public function json_hash() {
		return $this->object()->hash;
	}

	/**
	 * Get the package name index.
	 *
	 * @return array
	 */
	public function name_index() {
		if ( ! isset( $this->name_index ) ) {
			$this->generate_indices();
		}

		return $this->name_index;
	}

	/**
	 * Get the decoded json object.
	 *
	 * @return stdClass
	 */
	public function object() {
		if ( ! isset( $this->object ) ) {
			$this->object = $this->decode();

			$this->object->packages = array_map(
				[ $this, 'instantiate_package' ],
				$this->object->packages
			);

			$this->object->{'packages-dev'} = array_map(
				[ $this, 'instantiate_package' ],
				$this->object->{'packages-dev'}
			);
		}

		return $this->object;
	}

	/**
	 * Search for a single package by name.
	 *
	 * @param string $name A package name to search for.
	 *
	 * @return SSNepenthe\ComposerUtilities\LockPackage or null
	 */
	public function package_by_name( $name ) {
		if ( ! isset( $this->name_index()[ $name ] ) ) {
			return null;
		}

		$dev = $this->name_index()[ $name ]['dev'] ? 'packages-dev' : 'packages';
		$key = $this->name_index()[ $name ]['key'];

		return $this->object->{$dev}{$key};
	}

	/**
	 * Get an array of packages.
	 *
	 * @param bool $dev False to search in 'packages', true to search in 'dev-packages'.
	 *
	 * @return array
	 */
	public function packages( $dev = false ) {
		$key = $dev ? 'packages-dev' : 'packages';

		return $this->object()->{$key};
	}

	/**
	 * Get an array of packages based on package type.
	 *
	 * @param string $type Package type to search for.
	 *
	 * @return array
	 */
	public function packages_by_type( $type ) {
		if ( ! isset( $this->type_index()[ $type ] ) ) {
			return null;
		}

		$r = [];

		foreach ( $this->type_index()[ $type ] as $index ) {
			$dev = $index['dev'] ? 'packages-dev' : 'packages';
			$key = $index['key'];

			$r[] = $this->object->{$dev}[ $key ];
		}

		return $r;
	}

	/**
	 * Get the package type index.
	 *
	 * @return array
	 */
	public function type_index() {
		if ( ! isset( $this->type_index ) ) {
			$this->generate_indices();
		}

		return $this->type_index;
	}

	/**
	 * Generate package indices for easier package lookups.
	 */
	protected function generate_indices() {
		foreach ( $this->packages() as $key => $package ) {
			$this->name_index[ $package->name() ] = [
				'dev' => false,
				'key' => $key,
			];

			$this->type_index[ $package->type() ][] = [
				'dev' => false,
				'key' => $key,
			];
		}

		foreach ( $this->dev_packages() as $key => $package ) {
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
	 * Create a new LockPackage object from a given composer package object.
	 *
	 * @param stdClass $package composer.lock package.
	 *
	 * @return SSNepenthe\ComposerUtilities\LockPackage
	 */
	protected function instantiate_package( $package ) {
		return new Package( $package );
	}
}
