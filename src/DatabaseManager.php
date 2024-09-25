<?php

namespace Nacosvel\DatabaseManager;

use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;

class DatabaseManager implements DatabaseManagerInterface
{
    protected static mixed $manager;

    public function __construct(mixed $manager)
    {
        static::$manager = $manager;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([static::$manager, $method], $parameters);
    }

}
