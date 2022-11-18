<?php

namespace Inertia\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', static function () {
    helper('inertia');
});
