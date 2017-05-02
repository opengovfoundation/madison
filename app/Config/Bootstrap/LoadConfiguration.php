<?php
namespace App\Config\Bootstrap;

use App\Config\Repository\ConfigLoaderRepository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;

/**
 * Load Configuration Class
 *
 * Provides an addition to the standard laravel configuration load boostrapper.
 *
 * You will need to make the following changes:
 *
 * * Modify each of app/Console/Kernel.php and app/Http/Kernel.php to include
 *   the following bootstrappers function:
 *
 * <code>
 * protected function bootstrappers()
 * {
 *     $bootstrappers = parent::bootstrappers();
 *
 *     // Add the SiteConfig bootstrapper to the end.
 *     $bootstrappers[] = 'App\Config\Bootstrap\LoadConfiguration';
 *     return $bootstrappers;
 * }
 * </code>
 *
 * Note that App\Config\Bootstrap\LoadConfiguration replaces the original
 * line Illuminate\Foundation\Bootstrap\LoadConfiguration.  You may of course
 * already have a bootstrappers function in your Kernel.php files with other
 * bootstrappers replaced, in which case you just need to modify it to include
 * the updated LoadConfiguration bootstrapper code.
 */
class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        // Fetch the configuration that has already been loaded.
        $config = config();

        // Load the current configuration from the database and add it in to
        // the configuration loaded from files.
        $this->loadConfigurationDatabase($config);
    }

    /**
     * Load the configuration items from the database.
     *
     * @param  RepositoryContract  $config
     * @return void
     */
    protected function loadConfigurationDatabase(RepositoryContract $config)
    {
        // Bootstrap the Repository class
        $siteConfigRepository = new ConfigLoaderRepository();

        // Load the configuration into the current running config.
        $siteConfigRepository->setRunningConfiguration($config);
    }
}
