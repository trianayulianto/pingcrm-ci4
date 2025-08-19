<?php

namespace Inertia;

class Directive
{
    protected static $__inertiaSsr;

    /**
     * Compiles the inertia directive.
     *
     * @param  string  $id
     * @return string
     */
    public static function inertia($page, ?string $id = ''): string
    {
        $id = trim(trim($id), "\'\"") ?: 'app';
        $__inertiaSsr = static::__inertiaSsr($page);

        if ($__inertiaSsr instanceof \Inertia\Ssr\Response) {
            $template = $__inertiaSsr->body;
        } else {
            $template = '<div id="'.$id.'" data-page="'.htmlentities(json_encode($page)).'"></div>';
        }

        return implode(' ', array_map('trim', explode("\n", $template)));
    }

    /**
     * Compiles the inertiaHead directive.
     *
     * @param  string  $id
     * @return string
     */
    public static function inertiaHead($page): string
    {
        $__inertiaSsr = static::__inertiaSsr($page);

        if ($__inertiaSsr instanceof \Inertia\Ssr\Response) {
            $template = $__inertiaSsr->head;
        } else {
            $template = '';
        }

        return implode(' ', array_map('trim', explode("\n", $template)));
    }

    /**
     * Dispatch the Inertia page to the Server Side Rendering engine.
     *
     * @param  array  $page
     * @return Response|null
     */
    protected static function __inertiaSsr($page)
    {
        if (static::$__inertiaSsr === null) {
            $__inertiaSsr = \Config\Services::httpGateway()->dispatch($page);

            static::$__inertiaSsr = $__inertiaSsr;
        }

        return static::$__inertiaSsr;
    }
}
