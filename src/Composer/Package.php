<?php
/**
 * Convenience functionality for composer.lock packages.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities\Composer;

/**
 * This class wraps a single package from a JSON decoded composer.lock file.
 */
class Package {
	/**
	 * JSON decoded package from composer.lock.
	 *
	 * @var array
	 */
	protected $object;

	/**
	 * Set up our object.
	 *
	 * @param stdClass $object JSON decoded package from composer.lock.
	 */
	public function __construct( $object ) {
		$this->object = $object;
	}
	
	/**
	 * So we can use array_unique on a collection of packages.
	 * 
	 * @return string
	 */
	public function __toString() {
		return sprintf( '%s:%s', $this->name(), $this->version() );
	}

	/**
	 * Whether or not this package is of type 'composer-plugin'.
	 *
	 * @return boolean
	 */
	public function is_composer_plugin() {
		return $this->is_of_type( 'composer-plugin' );
	}

	/**
	 * Whether or not this package is of type 'library'.
	 *
	 * @return boolean
	 */
	public function is_library() {
		return $this->is_of_type( 'library' );
	}

	/**
	 * Determine whether or not this package is of the specified type.
	 *
	 * @param string $type Package type to check for.
	 *
	 * @return boolean
	 */
	public function is_of_type( $type ) {
		return $type === $this->type();
	}

	/**
	 * Get package name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->object->name;
	}

	/**
	 * Get package type.
	 *
	 * @return string
	 */
	public function type() {
		return $this->object->type;
	}

	/**
	 * Get package version.
	 *
	 * @return string
	 */
	public function version() {
		return $this->object->version;
	}
}
