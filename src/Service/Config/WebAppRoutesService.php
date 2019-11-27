<?php

namespace App\Service\Config;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Current configuration (routing etc) of web-interface (web app).
 */
class WebAppRoutesService implements WebAppRoutesInterface
{
    // all curent project routes
    private const ALL_CURRENT_ROUTES = [
		// 'user' => [
	    //     '/',
	    //     '/user',
	    //     '/login',
	    //     '/logout',
		// ],
		// 'admin' => [
        // 	'/admin',
		// ],
        '/',
        '/user',
        '/login',
        '/logout',
        '/admin',
    ];

    private $ALL_CURRENT_ROUTES;
    private $params;

    public function __construct()
    {
        // $this->ALL_CURRENT_ROUTES = $ALL_CURRENT_ROUTES;
        // $this->params = $params;
        return $this;
    }

    // return const allCurrentRoutes
    public function getRoutesList()
    {
        return implode(self::ALL_CURRENT_ROUTES);
        // return $this->ALL_CURRENT_ROUTES;
        // return $this->params;
        // $parameterValue = $this->params->get('someParam');
        // return $parameterValue;
    }

    public function checkRoute()
    {
    }
}
