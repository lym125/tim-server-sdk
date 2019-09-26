<?php

namespace Lym125\Tim;

use InvalidArgumentException;

class TimManager
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $ims = [];

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Attempt to get the im from the local cache.
     *
     * @param  string|null  $name
     *
     * @return \Lym125\Tim\Tim
     */
    public function im($name = null): Tim
    {
        $name = $name ?: 'default';

        return $this->ims[$name] ?? $this->ims[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given guard.
     *
     * @param  string  $name
     * @return \Lym125\Tim\Tim
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name): Tim
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Tim im [{$name}] is not defined.");
        }

        return new Tim($config);
    }

    /**
     * Get the im configuration.
     *
     * @param  string  $name
     *
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["tim.im.{$name}"];
    }

    /**
     * Dynamically call the default im instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->im()->{$method}(...$parameters);
    }
}
