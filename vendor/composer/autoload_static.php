<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit95e86778b1bbb9e5a67f5388ef139b0c
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Trin4ik\\DevinoApi\\' => 18,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Trin4ik\\DevinoApi\\' => 
        array (
            0 => __DIR__ . '/..' . '/trin4ik/laravel-devino-api/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit95e86778b1bbb9e5a67f5388ef139b0c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit95e86778b1bbb9e5a67f5388ef139b0c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
