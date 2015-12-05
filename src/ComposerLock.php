<?php

namespace SSNepenthe\ComposerUtilities;

class ComposerLock
{
    protected $lock;
    protected $cache = [
        'packages' => [
            'types' => [],
            'names' => [],
        ],
        'packages-dev' => [
            'types' => [],
            'names' => [],
        ],
    ];

    public function __construct($lock = 'composer.lock')
    {
        if (! is_string($lock)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be of type string, %s given.',
                    __METHOD__,
                    gettype($lock)
                )
            );
        }

        if (is_dir($lock)) {
            $lock .= '/composer.lock';
        }

        if (! is_file($lock)) {
            throw new \RuntimeException(
                'Supplied lock file does not exist.'
            );
        }

        $this->lock = json_decode(file_get_contents($lock));
    }

    public function cache()
    {
        return $this->cache;
    }

    public function devPackages()
    {
        return $this->packages(true);
    }

    public function filterPackagesByName($name, $dev = false)
    {
        if (! is_string($name)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be of type string, %s given.',
                    __METHOD__,
                    gettype($name)
                )
            );
        }

        $key = $dev ? 'packages-dev' : 'packages';

        if (! isset($this->cache[ $key ]['names'][ $name ])) {
            $this->setPackageNameCache($key, $name, $dev);
        }

        return isset($this->cache[ $key ]['names'][ $name ]) ?
            $this->cache[ $key ]['names'][ $name ] :
            null;
    }

    public function filterPackagesByType($type, $dev = false)
    {
        if (! is_string($type)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be of type string, %s given.',
                    __METHOD__,
                    gettype($type)
                )
            );
        }

        $key = $dev ? 'packages-dev' : 'packages';

        if (! isset($this->cache[ $key ]['types'][ $type ])) {
            $this->setPackageTypeCache($key, $type, $dev);
        }

        return isset($this->cache[ $key ]['types'][ $type ]) ?
            $this->cache[ $key ]['types'][ $type ] :
            null;
    }

    public function filterWordPressPackagesByType($type, $dev = false)
    {
        if ('wordpress-' !== substr($type, 0, 10)) {
            $type = 'wordpress-' . $type;
        }

        return $this->filterPackagesByType($type, $dev);
    }

    public function filterWpackagistPackagesByName($name, $dev = false)
    {
        if ('wpackagist-' !== substr($name, 0, 11)) {
            $name = 'wpackagist-' . $name;
        }

        return $this->filterPackagesByName($name, $dev);
    }

    public function hash()
    {
        return $this->lock->hash;
    }

    public function packages($dev = false)
    {
        if ($dev) {
            return $this->lock->{'packages-dev'};
        }

        return $this->lock->packages;
    }

    protected function setPackageNameCache($key, $name, $dev)
    {
        foreach ($this->packages($dev) as $package) {
            if ($name !== $package->name) {
                continue;
            }

            $this->cache[ $key ]['names'][ $name ] = $package;
        }
    }

    protected function setPackageTypeCache($key, $type, $dev)
    {
        foreach ($this->packages($dev) as $package) {
            if ($type !== $package->type) {
                continue;
            }

            $this->cache[ $key ]['types'][ $type ][] = $package;
        }
    }
}
