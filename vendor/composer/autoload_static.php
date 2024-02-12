<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5fbaf900a7e04b55ac678ed4825f6cea
{
    public static $prefixesPsr0 = array (
        'G' => 
        array (
            'GingerTek' => 
            array (
                0 => __DIR__ . '/..' . '/ginger-tek/routy/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit5fbaf900a7e04b55ac678ed4825f6cea::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit5fbaf900a7e04b55ac678ed4825f6cea::$classMap;

        }, null, ClassLoader::class);
    }
}
