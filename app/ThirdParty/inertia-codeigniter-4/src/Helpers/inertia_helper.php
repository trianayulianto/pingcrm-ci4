<?php

use Inertia\Config\Services;
use Inertia\Factory;

if (! function_exists('inertia')) {
    /**
     * @param  null  $component
     * @param  array  $props
     * @return Factory|string
     */
    function inertia($component = null, $props = [])
    {
        $inertia = Services::inertia();

        if ($component) {
            return $inertia->render($component, $props);
        }

        return $inertia;
    }
}

if (! function_exists('array_only')) {
    /**
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if (! function_exists('array_get')) {
    /**
     * @param  null  $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (! is_array($array)) {
            return closure_call($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? closure_call($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return closure_call($default);
            }
        }

        return $array;
    }
}

if (! function_exists('array_set')) {
    /**
     * @return array|mixed
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (! function_exists('closure_call')) {
    /**
     * @param  array  $params
     * @return mixed
     */
    function closure_call($closure, $params = [])
    {
        return $closure instanceof Closure ? $closure(...$params) : $closure;
    }
}

if (! function_exists('array_forget')) {
    /**
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    function array_forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (array_exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && array_accessible($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }
}

if (! function_exists('array_accessible')) {
    /**
     * @param  mixed  $value
     * @return bool
     */
    function array_accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}

if (! function_exists('array_exists')) {
    /**
     * @param  mixed  $array
     * @param  mixed  $key
     * @return bool
     */
    function array_exists($array, $key)
    {
        if (is_null($key)) {
            return false;
        }

        if (is_array($array) || $array instanceof ArrayAccess) {
            return isset($array[$key]);
        }

        return array_key_exists($key, $array);
    }
}

if (! function_exists('str_after')) {
    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function str_after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}

if (! function_exists('str_before')) {
    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function str_before($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, (string) $search, true);

        return $result === false ? $subject : $result;
    }
}

if (! function_exists('str_finish')) {
    /**
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    function str_finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }
}

if (! function_exists('str_start')) {
    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function str_start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }
}
