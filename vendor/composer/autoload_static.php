<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit294593d481daae0374801881d59bb96e
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GurmeHub\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GurmeHub\\' => 
        array (
            0 => __DIR__ . '/..' . '/gurmehub/plugin-helper/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit294593d481daae0374801881d59bb96e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit294593d481daae0374801881d59bb96e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit294593d481daae0374801881d59bb96e::$classMap;

        }, null, ClassLoader::class);
    }
}
