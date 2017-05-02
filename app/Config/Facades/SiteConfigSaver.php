<?php
/**
 * SiteConfigSaver facade
 */
namespace App\Config\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SiteConfigSaver
 *
 * Facade class for accessing the site config saver.
 *
 * Note that this facade only accesses the methods for CRUD (loading/saving)
 * the raw config data.  Otherwise this package just uses the standard Laravel
 * Config facade to access the configuration data, because the bootstrapper
 * for the package loads the database site config into the Laravel config.
 *
 * ### Example
 *
 * <code>
 * $config = SiteConfigSaver::get();
 * </code>
 *
 * @see  App\Config\Repository\ConfigSaverRepository
 */
class SiteConfigSaver extends Facade
{
    /**
     * Get the registered component.
     *
     * @return object
     */
    protected static function getFacadeAccessor()
    {
        return 'siteconfig';
    }
}
