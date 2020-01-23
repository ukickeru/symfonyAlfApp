<?php


namespace App\Service\Request\CheckRoute\GetRoutesConfig;


class GetRoutesConfigService
{

    private $allCurrentRoutes; // all current project routes

    public function __construct(array $allCurrentRoutes)
    {
        $this->allCurrentRoutes = $allCurrentRoutes;
        return $this;
    }

    // return const allCurrentRoutes
    public function getRoutesList()
    {
        return implode($this->allCurrentRoutes);
    }

}