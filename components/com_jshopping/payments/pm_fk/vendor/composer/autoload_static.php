<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit66d90eaada11d80c795258c5b8b90d75
{
    public static $files = array (
        'ab382a10c8511339af72dbf4f41802af' => __DIR__ . '/../..' . '/helpers/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Suvarivaza\\JoomShopping\\FreeKassa\\' => 34,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Suvarivaza\\JoomShopping\\FreeKassa\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit66d90eaada11d80c795258c5b8b90d75::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit66d90eaada11d80c795258c5b8b90d75::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit66d90eaada11d80c795258c5b8b90d75::$classMap;

        }, null, ClassLoader::class);
    }
}
