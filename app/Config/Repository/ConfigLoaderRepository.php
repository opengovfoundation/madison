<?php
/**
 * Class ConfigLoaderRepository
 */

namespace App\Config\Repository;

use App\Config\Models\Config as ConfigModel;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Support\Facades\Cache;

/**
 * Class ConfigLoaderRepository
 *
 * This contains all of the functionality to load configuration
 * values from the database and put them together into a format
 * that can be stored in the application configuration by the
 * bootstrapper.
 */
class ConfigLoaderRepository
{
    /** @var  string */
    protected $environment;

    /** @var  string */
    protected $cache_key;

    /**
     * Public accessor for environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Public accessor for cache key
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cache_key;
    }

    /**
     * Store the loaded config in the cache.
     *
     * @param mixed $config
     * @return void
     */
    public function storeConfig($config)
    {
        Cache::put($this->getCacheKey(), $config, 60);
    }

    /**
     * Fetch the stored config from the cache.
     *
     * @return mixed
     */
    public function fetchConfig()
    {
        if (Cache::has($this->getCacheKey())) {
            return Cache::get($this->getCacheKey());
        }

        return null;
    }

    /**
     * Forget the cache.
     *
     * @return void
     */
    public function forgetConfig()
    {
        /** @var ConfigLoaderRepository $repository */
        $repository = $this->loadEnvironment();
        Cache::forget($repository->getCacheKey());
    }

    /**
     * Fetch all configuration groups.
     *
     * @return array
     */
    public function fetchAllGroups()
    {
        $groups = ConfigModel::fetchAllGroups();
        return $groups;
    }

    /**
     * Loads environment
     *
     * @return ConfigLoaderRepository
     */
    public function loadEnvironment()
    {
        // Fetch the current application environment.
        $this->environment = app()->environment();

        $this->cache_key = 'config.' . $this->environment;

        return $this;
    }

    /**
     * Load the database backed configuration.
     *
     * This function does the work of loading the entire site configuration
     * from the database and returns it as a group => [key=>value] set.  The
     * configuration is retrieved from cache if it already exists there, and
     * stored into the cache after it is loaded.
     *
     * @return array
     */
    public function loadConfiguration()
    {
        /** @var ConfigLoaderRepository $repository */
        $repository = $this->loadEnvironment();

        $cache = $repository->fetchConfig();
        if (! empty($cache)) {
            return $cache;
        }

        $config = [];

        // Fetch all groups, and fetch the config for each group based on the
        // environment
        foreach ($repository->fetchAllGroups() as $group) {
            $groupConfig = ConfigModel::fetchSettings(
                $repository->getEnvironment(),
                $group
            );
            $config[$group] = $groupConfig;
        }

        $repository->storeConfig($config);
        return $config;
    }

    /**
     * Load the database backed configuration and save it
     *
     * Load the database backed configuration and save it
     * into the current running config
     *
     * @param RepositoryContract $config
     * @return void
     */
    public function setRunningConfiguration(RepositoryContract $config)
    {
        // Load the configuration into the current running config.
        foreach ($this->loadConfiguration() as $group => $groupConfig) {
            foreach ($groupConfig as $key => $value) {
                $config->set($group.'.'.$key, $value);
            }
        }
    }
}
