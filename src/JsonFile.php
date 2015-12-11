<?php
/**
 * Convenience function for json files.
 *
 * @package composer-utilities
 */

namespace SSNepenthe\ComposerUtilities;

/**
 * This class wraps a json file.
 */
class JsonFile {
	/**
	 * Path to a json file.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Contents of the json file.
	 *
	 * @var string
	 */
	protected $json;

	/**
	 * Json decoded object.
	 *
	 * @var stdClass
	 */
	protected $object;

	/**
	 * Set up the object.
	 *
	 * @throws \RuntimeException If the file does not exist.
	 *
	 * @param string $path Path to a json file.
	 */
	public function __construct( $path ) {
		$this->path = $path;

		if ( ! $this->exists() ) {
			throw new \RuntimeException( $this->path . ' is not a valid file' );
		}
	}

	/**
	 * Get the contents of the file at path passed to the constructor.
	 *
	 * @return string
	 */
	public function json() {
		if ( ! isset( $this->json ) ) {
			$this->json = $this->read();
		}

		return $this->json;
	}

	/**
	 * Get the json decoded object.
	 *
	 * @return stdClass
	 */
	public function object() {
		if ( ! isset( $this->object ) ) {
			$this->object = $this->decode();
		}

		return $this->object;
	}

	/**
	 * Get the path passed to the constructor.
	 *
	 * @return string
	 */
	public function path() {
		return $this->path;
	}

	/**
	 * Json decode the file at the path passes to the constructor.
	 *
	 * @throws \RuntimeException If json_decode() fails or there is a json error.
	 *
	 * @return stdClass
	 */
	protected function decode() {
		$object = json_decode( $this->json() );

		if ( ! $this->valid( $object ) ) {
			throw new \RuntimeException( $this->path . ' does not contain valid JSON' );
		}

		return $object;
	}

	/**
	 * Check if the file exists.
	 *
	 * @return bool
	 */
	protected function exists() {
		return is_file( $this->path );
	}

	/**
	 * Read the file at the path passed to the constructor.
	 *
	 * @return string
	 */
	protected function read() {
		return file_get_contents( $this->path );
	}

	/**
	 * Check if json_decode was successful.
	 *
	 * @param stdClass or null $object Json decoded object.
	 *
	 * @return bool
	 */
	protected function valid( $object ) {
		if ( null === $object || JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		return true;
	}
}
