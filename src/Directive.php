<?php

namespace Inertia;

class Directive
{
    /**
     * Compiles the inertia directive.
     *
     * @param  string  $id
     * @return string
     */
    public static function inertia($page, ?string $id = ''): string
    {
        $id = trim(trim($id), "\'\"") ?: 'app';
        $__inertiaSsr = \Config\Services::httpGateway()->dispatch($page);

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
        $__inertiaSsr = \Config\Services::httpGateway()->dispatch($page);

        if ($__inertiaSsr instanceof \Inertia\Ssr\Response) {
            $template = $__inertiaSsr->head;
        } else {
            $template = '';
        }

        return implode(' ', array_map('trim', explode("\n", $template)));
    }
}
