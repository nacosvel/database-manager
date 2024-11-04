<?php

namespace Nacosvel\DatabaseManager;

use Closure;
use InvalidArgumentException;
use RuntimeException;

abstract class MultipleInstanceManager
{
    /**
     * The application instance.
     */
    protected mixed $app;

    /**
     * The array of resolved instances.
     *
     * @var array
     */
    protected array $instances = [];

    /**
     * The registered custom instance creators.
     *
     * @var array
     */
    protected array $customCreators = [];

    /**
     * Create a new manager instance.
     *
     * @param mixed $app
     *
     * @return void
     */
    public function __construct(mixed $app)
    {
        $this->app = $app;
    }

    /**
     * Get the default instance name.
     *
     * @return string
     */
    abstract public function getDefaultInstance(): string;

    /**
     * Set the default instance name.
     *
     * @param string $name
     *
     * @return void
     */
    abstract public function setDefaultInstance(string $name): void;

    /**
     * Get the instance specific configuration.
     *
     * @param string $name
     *
     * @return array|null
     */
    abstract public function getInstanceConfig(string $name): ?array;

    /**
     * Get an instance by name.
     *
     * @param string|null $name
     *
     * @return mixed
     */
    public function instance(string $name = null): mixed
    {
        $name = $name ?: $this->getDefaultInstance();

        return $this->instances[$name] = $this->get($name);
    }

    /**
     * Attempt to get an instance from the local cache.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function get(string $name): mixed
    {
        return $this->instances[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given instance.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name): mixed
    {
        $config = $this->getInstanceConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Instance [{$name}] is not defined.");
        }

        if (!array_key_exists('driver', $config)) {
            throw new RuntimeException("Instance [{$name}] does not specify a driver.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        if (method_exists($this, $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver')) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Instance driver [{$config['driver']}] is not supported.");
    }

    /**
     * Call a custom instance creator.
     *
     * @param array $config
     *
     * @return mixed
     */
    protected function callCustomCreator(array $config): mixed
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Unset the given instances.
     *
     * @param array|string|null $name
     *
     * @return static
     */
    public function forgetInstance(array|string $name = null): static
    {
        $name ??= $this->getDefaultInstance();

        foreach ((array)$name as $instanceName) {
            if (isset($this->instances[$instanceName])) {
                unset($this->instances[$instanceName]);
            }
        }

        return $this;
    }

    /**
     * Disconnect the given instance and remove from local cache.
     *
     * @param string|null $name
     *
     * @return void
     */
    public function purge(string $name = null): void
    {
        $name ??= $this->getDefaultInstance();

        unset($this->instances[$name]);
    }

    /**
     * Register a custom instance creator Closure.
     *
     * @param string  $name
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $name, Closure $callback): static
    {
        $this->customCreators[$name] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Dynamically call the default instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->instance()->$method(...$parameters);
    }

}
