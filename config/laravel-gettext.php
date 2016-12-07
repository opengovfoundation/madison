<?php

return [

    /**
     * Translation handlers, options are:
     *
     * - symfony: (recommended) uses the symfony translations component. Incompatible with php-gettext
     * you must uninstall the php-gettext module before use this handler.
     *
     * - gettext: requires the php-gettext module installed. This handler has well-known cache issues
     */
    'handler' => 'symfony',

    /**
     * Session identifier: Key under which the current locale will be stored.
     */
    'session-identifier' => 'laravel-gettext-locale',

    /**
     * Default locale: this will be the default for your application.
     * Is to be supposed that all strings are written in this language.
     */
    'locale' => 'en_US',

    /**
     * Supported locales: An array containing all allowed languages
     */
    'supported-locales' => [
        'en_US',
    ],

    /**
     * Default charset encoding.
     */
    'encoding' => 'UTF-8',

    /**
     * -----------------------------------------------------------------------
     * All standard configuration ends here. The following values
     * are only for special cases.
     * -----------------------------------------------------------------------
     **/

    /**
     * Locale categories to set
     */
    'categories' => [
        'LC_ALL',
    ],

    /**
     * Base translation directory path (don't use trailing slash)
     */
    'translations-path' => '../resources/lang',

    /**
     * Relative path to the app folder: is used on .po header files
     */
    'relative-path' => '../../../../../app',

    /**
     * Fallback locale: When default locale is not available
     */
    'fallback-locale' => 'en_US',

    /**
     * Default domain used for translations: It is the file name for .po and .mo files
     */
    'domain' => 'messages',

    /**
     * Project name: is used on .po header files
     */
    'project' => 'MultilanguageLaravelApplication',

    /**
     * Translator contact data (used on .po headers too)
     */
    'translator' => 'James Translator <james@translations.colm>',

    /**
     * Paths where Poedit will search recursively for strings to translate.
     * All paths are relative to app/ (don't use trailing slash).
     *
     * Remember to call artisan gettext:update after change this.
     */
    'source-paths' => [
        'Http',
        '../resources/views',
        'Console',
    ],

    /**
     * Multi-domain directory paths. If you want the translations in
     * different files, just wrap your paths into a domain name.
     * for example:
     */
    /*
    'source-paths' => [

        // 'frontend' domain
        'frontend' => [
			'controllers',
			'views/frontend',
		],

        // 'backend' domain
		'backend' => [
			'views/backend',
		],

        // 'messages' domain (matches default domain)
		'storage/views',
	],
    */

    /**
     * Sync laravel: A flag that determines if the laravel built-in locale must
     * be changed when you call LaravelGettext::setLocale.
     */
    'sync-laravel' => true,

    /**
     * The adapter used to sync the laravel built-in locale
     */
    'adapter' => \Xinax\LaravelGettext\Adapters\LaravelAdapter::class,

    /**
     * Use custom locale that is not supported by the system
     */
    'custom-locale' => false,

    /**
     * The keywords list used by poedit to search the strings to be translated
     *
     * The "_", "__" and "gettext" are singular translation functions
     * The "_n" and "ngettext" are plural translation functions
     * The "dgettext" function allows a translation domain to be explicitly specified
     *
     * "__" and "_n" are helpers functions @see \Xinax\LaravelGettext\Support\helpers.php
     */
    'keywords-list' => ['_', '__', 'gettext', '_n:1,2', 'ngettext:1,2', 'dgettext:2'],
];
