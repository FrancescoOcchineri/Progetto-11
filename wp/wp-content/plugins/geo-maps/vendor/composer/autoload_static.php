<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteed6bc6255ea96f09043767c8dc57657
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MatrixAddons\\GeoMaps\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MatrixAddons\\GeoMaps\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteed6bc6255ea96f09043767c8dc57657::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteed6bc6255ea96f09043767c8dc57657::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticIniteed6bc6255ea96f09043767c8dc57657::$classMap;

        }, null, ClassLoader::class);
    }
}