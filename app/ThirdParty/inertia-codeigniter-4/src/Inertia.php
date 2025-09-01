<?php

namespace Inertia;

use Inertia\Config\Services;

/**
 * @method static void setRootView(string $name)
 * @method static void share(string|array<array-key, mixed> $key, mixed $value = null)
 * @method static mixed getShared(string|null $key = null, mixed $default = null)
 * @method static void clearHistory()
 * @method static void encryptHistory($encrypt = true)
 * @method static void flushShared()
 * @method static void version(\Closure|string|null $version)
 * @method static string getVersion()
 * @method static void resolveUrlUsing(\Closure|null $urlResolver = null)
 * @method static \Inertia\OptionalProp optional(callable $callback)
 * @method static \Inertia\LazyProp lazy(callable $callback)
 * @method static \Inertia\DeferProp defer(callable $callback, string $group = 'default')
 * @method static \Inertia\AlwaysProp always(mixed $value)
 * @method static \Inertia\MergeProp merge(mixed $value)
 * @method static \Inertia\MergeProp deepMerge(mixed $value)
 * @method static \Inertia\Response render(string $component, array<array-key, mixed> $props = [])
 * @method static \CodeIgniter\HTTP\Response location(string $url)
 *
 * @see Factory
 */
class Inertia
{
    public static function __callStatic($method, $arguments)
    {
        return Services::getSharedInstance('inertia')->{$method}(...$arguments);
    }
}
