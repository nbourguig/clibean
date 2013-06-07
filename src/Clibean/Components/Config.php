<?php
/**
 * A simple wrapper class for the Laravel Config package
 */

namespace Clibean\Components;


use Illuminate\Config\FileLoader as Laravel_FileLoader;
use Illuminate\Config\Repository as Laravel_Config;
use Illuminate\Container\Container as Laravel_Container;
use Illuminate\Filesystem\Filesystem as Laravel_FileSystem;


class Config
{
    protected static $env = '';
    protected static $configDirectory = '';
    public static $container = null;

    public static function setEnvironnement($env)
    {
        static::$env = $env;
    }

    public static function setConfigDirectory($configDirectory)
    {
        static::$configDirectory = $configDirectory;
    }


    public static function detectEnvironnement($dir)
    {
        // set convention config directory
        static::setConfigDirectory($dir . '/config');

        if (file_exists($dir . '/.env'))
        {
            $env = str_replace("\n", '', file_get_contents('.env'));
            if (!file_exists(static::$configDirectory . '/' . $env . '/app.php'))
            {
                throw new \Exception("Unknown environnement (maybe there's no {$env}/app.php) !");
            }
        }
        else
        {
            throw new \Exception(".env file required !");
        }

        // Set detected environnement
        static::$env = $env;
    }


    /**
     * Passes calls through to the Connection object.
     *
     * @param   string  The method name
     * @param   array   The method parameters sent
     * @return  mixed   The result of the call
     */
    public static function __callStatic($method, $parameters)
    {
        if (!static::$container)
        {
            if (empty(static::$configDirectory) || empty(static::$env))
            {
                throw new \Exception('Config Directory and Environnement must be set before using the class.');
            }

            static::boot();
        }

        return call_user_func_array(
            array(
                static::$container->make('config'),
                $method
            ),
            $parameters
        );
    }

    protected static function boot()
    {
        $env             = static::$env;
        $configDirectory = static::$configDirectory;

        static::$container = new Laravel_Container();
        static::$container->singleton('config', function () use ($env, $configDirectory)
        {
            return new Laravel_Config(
                new Laravel_FileLoader(new Laravel_FileSystem, $configDirectory),
                $env
            );
        });
    }

    public static function getClibeanArt()
    {
        return <<< "ART"
   ____ _ _ _
  / ___| (_) |__   ___  __ _ _ __
 | |   | | | '_ \ / _ \/ _` | '_ \
 | |___| | | |_) |  __/ (_| | | | |
  \____|_|_|_.__/ \___|\__,_|_| |_|
ART;
    }

    public static function set($key, $value)
    {
        $config = static::$container->make('config');
        $config->set($key, $value);
    }

    //
    // Environnement helpers
    //

    public static function getEnvironement()
    {
        return static::$env;
    }

    public static function isEnvironement($env)
    {
        return static::$env == $env;
    }


    public static function getWorkingDirectory()
    {
        return rtrim(APP_DIR, '/');
    }

    public static function getApplicationDirectory()
    {
        return rtrim(APP_DIR, '/');
    }


}