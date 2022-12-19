<?php

namespace Inertia\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function inertia($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('inertia');
        }

        return new \Inertia\Factory;
    }

    public static function httpGateway($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('httpGateway');
        }

        return new \Inertia\Ssr\HttpGateway;
    }
}
