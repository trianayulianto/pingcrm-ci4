<?php

namespace Inertia\Config;

use CodeIgniter\Config\BaseConfig;

class Inertia extends BaseConfig
{
    /*
    |--------------------------------------------------------------------------
    | Server Side Rendering
    |--------------------------------------------------------------------------
    |
    | These options configures if and how Inertia uses Server Side Rendering
    | to pre-render the initial visits made to your application's pages.
    |
    | Do note that enabling these options will NOT automatically make SSR work,
    | as a separate rendering service needs to be available. To learn more,
    | please visit https://inertiajs.com/server-side-rendering
    |
    */
    public $ssr = [
        'enabled' => false,
        'url' => 'http://127.0.0.1:13714/render',
    ];
}
