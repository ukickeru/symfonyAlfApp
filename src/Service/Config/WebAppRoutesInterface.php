<?php

namespace App\Service\Config;

/**
 * Interface for ConfigService.php.
 */
interface WebAppRoutesInterface
{
    // public function __construct();

    public function getRoutesList();

    public function checkRoute();
}
