<?php

namespace SSNepenthe\ComposerUtilities;

class ComposerJSON
{
    protected $hash;
    protected $json;
    protected $paths;

    public function __construct($json = 'composer.json')
    {
        if (! is_string($json)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be of type string, %s given.',
                    __METHOD__,
                    gettype($json)
                )
            );
        }

        if (is_dir($json)) {
            $json .= '/composer.json';
        }

        if (! is_file($json)) {
            throw new \RuntimeException(
                'Supplied json file does not exist.'
            );
        }

        $file = file_get_contents($json);

        $this->hash = md5($file);
        $this->json = json_decode($file);
    }

    public function filterPathsByName($name)
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

        return isset($this->paths()[ $name ]) ? $this->paths()[ $name ] : null;
    }

    public function hash()
    {
        return $this->hash;
    }

    public function paths()
    {
        if (! isset($this->paths)) {
            $this->setPaths();
        }

        return $this->paths;
    }

    public function muPluginPath()
    {
        return $this->filterPathsByName('wordpress-muplugin');
    }

    public function pluginPath()
    {
        return $this->filterPathsByName('wordpress-plugin');
    }

    public function themePath()
    {
        return $this->filterPathsByName('wordpress-theme');
    }

    public function vendorPath()
    {
        return $this->filterPathsByName('vendor-dir');
    }

    public function wordPressPath()
    {
        return $this->filterPathsByName('wordpress-install-dir');
    }

    protected function setPaths()
    {
        $config = isset($this->json->config) ?
            $this->json->config :
            false;
        $extra = isset($this->json->extra) ?
            $this->json->extra :
            false;
        $installer_paths = $extra && isset($extra->{'installer-paths'}) ?
            $extra->{'installer-paths'} :
            [];

        $this->paths['vendor-dir'] = $config && isset($config->{'vendor-dir'}) ?
            $config->{'vendor-dir'} :
            'vendor';
        $this->paths['wordpress-install-dir'] = $extra && isset($extra->{'wordpress-install-dir'}) ?
            $extra->{'wordpress-install-dir'} :
            'wordpress';

        foreach ($installer_paths as $path => $types) {
            $types = array_map([$this, 'stripType'], $types);

            foreach ($types as $type) {
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

    protected function stripType($value)
    {
        if ('type:' === substr($value, 0, 5)) {
            $value = str_replace('type:', '', $value);
        }

        return $value;
    }
}
